<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;

use App\Imports\LeadImporter;
use App\Jobs\ImportLeads;
use App\Models\LeadImport;
use App\Models\LeadSource;
use App\Models\User;
use App\Support\Spreadsheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Upload leads from Excel or CSV: everyday uploads and the one-time Leadrat import.
 * Upload, check which column is which, then the import runs in the background.
 */
class ImportController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->manager($request);

        return view('crm.imports.index', [
            'imports' => LeadImport::with('user:id,name')
                ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
                ->latest()->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        $user = $this->manager($request);

        // Say exactly why PHP rejected the upload (size limit, missing temp folder, ...).
        $upload = $request->file('file');
        if ($upload && ! $upload->isValid()) {
            return back()->withErrors(['file' => 'The file did not upload: '.$upload->getErrorMessage()]);
        }

        $request->validate(
            ['file' => ['required', 'file', 'extensions:xlsx,csv', 'max:51200']],
            ['file.uploaded' => 'The file did not upload. It may be bigger than the server allows (check upload_max_filesize in php.ini).',
                'file.extensions' => 'Please upload an Excel (.xlsx) or .csv file. In Excel use File > Save As > Excel Workbook.']
        );

        $file = $request->file('file');
        $path = $file->storeAs('imports', Str::random(32).'.'.strtolower($file->getClientOriginalExtension()), 'local');

        $headers = null;
        $preview = [];
        $total = 0;
        try {
            foreach (Spreadsheet::rows(Storage::disk('local')->path($path)) as $row) {
                if ($headers === null) {
                    $headers = array_map(fn ($h) => Str::limit(trim((string) $h), 60), $row);

                    continue;
                }
                $total++;
                if (count($preview) < 3 && ! Spreadsheet::isEmptyRow($row)) {
                    $preview[] = array_map(fn ($v) => $v instanceof \DateTimeInterface ? $v->format('d-m-Y H:i') : Str::limit((string) $v, 40), $row);
                }
            }
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);

            return back()->withErrors(['file' => 'This file could not be read. Open it in Excel and save it again as .xlsx, then upload that.']);
        }

        if (! $headers || ! array_filter($headers) || $total === 0) {
            Storage::disk('local')->delete($path);

            return back()->withErrors(['file' => 'The file is empty. The first row should have column names and the leads should start on row 2.']);
        }

        $import = LeadImport::create([
            'user_id' => $user->id,
            'file_name' => Str::limit($file->getClientOriginalName(), 200),
            'path' => $path,
            'status' => 'mapping',
            'headers' => $headers,
            'preview' => $preview,
            'mapping' => LeadImporter::guessMapping($headers),
            'total_rows' => $total,
        ]);

        return redirect()->route('crm.imports.show', $import);
    }

    public function show(Request $request, LeadImport $import)
    {
        $this->authorizeImport($request, $import);

        return view('crm.imports.show', [
            'import' => $import,
            'fields' => LeadImporter::FIELDS,
            'sources' => LeadSource::where('is_active', true)->orderBy('sort_order')->get(['id', 'name']),
            'owners' => User::assignableBy($request->user()),
            'defaultSourceId' => LeadSource::where('slug', 'excel-upload')->value('id'),
        ]);
    }

    public function start(Request $request, LeadImport $import)
    {
        $user = $this->authorizeImport($request, $import);
        abort_unless($import->status === 'mapping', 409, 'This import has already started.');

        $columns = array_keys($import->headers);
        $rules = ['map' => ['array'], 'map.phone' => ['required', Rule::in($columns)]];
        foreach (array_keys(LeadImporter::FIELDS) as $field) {
            $rules["map.$field"] ??= ['nullable', Rule::in($columns)];
        }
        $data = $request->validate($rules + [
            'default_source_id' => ['nullable', 'exists:lead_sources,id'],
            'default_owner_id' => ['nullable', Rule::in(User::assignableBy($user)->pluck('id'))],
            'skip_closed' => ['nullable', 'boolean'],
            'skip_before' => ['nullable', 'date'],
        ], ['map.phone.required' => 'Pick the column that has the phone number.']);

        $mapping = [];
        foreach (array_keys(LeadImporter::FIELDS) as $field) {
            $col = $data['map'][$field] ?? null;
            $mapping[$field] = ($col === null || $col === '') ? null : (int) $col;
        }

        $import->update([
            'status' => 'queued',
            'mapping' => $mapping,
            'options' => [
                'default_source_id' => $data['default_source_id'] ?? null,
                'default_owner_id' => $data['default_owner_id'] ?? null,
                'skip_closed' => (bool) ($data['skip_closed'] ?? false),
                'skip_before' => $data['skip_before'] ?? null,
            ],
        ]);

        ImportLeads::dispatch($import->id);

        return redirect()->route('crm.imports.show', $import);
    }

    /** The rows that could not be imported, to fix in Excel and upload again. */
    public function problems(Request $request, LeadImport $import)
    {
        $this->authorizeImport($request, $import);

        return response()->streamDownload(function () use ($import) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // so Excel reads it as UTF-8
            fputcsv($out, ['Row in your file', 'Name', 'Phone', 'Problem']);
            foreach ($import->problems ?? [] as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, 'problem-rows-'.$import->id.'.csv', ['Content-Type' => 'text/csv']);
    }

    /** A blank sheet with the columns KeyLeads understands. */
    public function template(Request $request)
    {
        $this->manager($request);

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Name', 'Phone', 'Alternate Phone', 'Email', 'Source', 'Sub Source', 'Project', 'Status', 'Sub Status',
                'Assigned To', 'Notes', 'Received Date', 'Budget', 'BHK', 'Property Type', 'Location']);
            fputcsv($out, ['Ravi Kumar', '9876543210', '', 'ravi@example.com', 'Website', '', 'Green Meadows', 'New', '',
                '', 'Wants 2 BHK near station', now()->format('d-m-Y'), '75 L', '2', 'Flat', 'Thane West']);
            fclose($out);
        }, 'keyleads-upload-template.csv', ['Content-Type' => 'text/csv']);
    }

    private function manager(Request $request): User
    {
        $user = $request->user();
        abort_if($user->role === User::ROLE_SALES, 403, 'Only managers and admins can upload leads.');

        return $user;
    }

    private function authorizeImport(Request $request, LeadImport $import): User
    {
        $user = $this->manager($request);
        abort_unless($user->isAdmin() || $import->user_id === $user->id, 403);

        return $user;
    }
}

<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\IssuedLetter;
use App\Models\LetterTemplate;
use App\Models\User;
use App\Services\LeaveService;
use App\Support\LetterText;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** HR letters: templates with fill-ins, issue to a person, PDF. People see their own letters. */
class LetterController extends Controller
{
    public function index(Request $request)
    {
        $this->hrOnly($request);
        $templates = LetterTemplate::orderByDesc('active')->orderBy('name')->get();
        $q = IssuedLetter::with(['user', 'issuer'])->latest('letter_date')->latest('id');
        if ($request->filled('user_id')) {
            $q->where('user_id', $request->user_id);
        }
        $letters = $q->paginate(25)->withQueryString();
        $people = User::orderByRaw("status = 'Active' desc")->orderBy('name')->get(['id', 'name', 'employee_code', 'status']);

        return view('hr.letters.index', compact('templates', 'letters', 'people'));
    }

    public function editTemplate(Request $request, ?LetterTemplate $template = null)
    {
        $this->hrOnly($request);
        $template ??= new LetterTemplate(['type' => 'other', 'active' => true]);

        return view('hr.letters.template', ['template' => $template, 'fields' => LetterText::FIELDS]);
    }

    public function saveTemplate(Request $request, ?LetterTemplate $template = null)
    {
        $this->hrOnly($request);
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:'.implode(',', array_keys(LetterTemplate::TYPES)),
            'subject' => 'nullable|string|max:190',
            'body' => 'required|string|max:20000',
        ]);
        $data['active'] = $request->boolean('active');
        $template && $template->exists ? $template->update($data) : LetterTemplate::create($data);

        return redirect()->route('letters.index')->with('success', 'Template saved.');
    }

    /** Step 1: pick person and template. Step 2: check the filled-in text and issue. */
    public function create(Request $request)
    {
        $this->hrOnly($request);
        $templates = LetterTemplate::where('active', true)->orderBy('name')->get();
        $people = User::orderByRaw("status = 'Active' desc")->orderBy('name')->get(['id', 'name', 'employee_code', 'status']);
        $person = $request->filled('user_id') ? User::find($request->user_id) : null;
        $template = $request->filled('template_id') ? LetterTemplate::find($request->template_id) : null;
        $filled = null;
        $blanks = [];
        if ($person && $template) {
            $filled = [
                'subject' => LetterText::fill($template->subject, $person),
                'body' => LetterText::fill($template->body, $person),
            ];
            $blanks = LetterText::blanks($template->subject."\n".$template->body, $person);
        }

        return view('hr.letters.create', compact('templates', 'people', 'person', 'template', 'filled', 'blanks'));
    }

    public function store(Request $request)
    {
        $me = $this->hrOnly($request);
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'letter_template_id' => 'nullable|exists:letter_templates,id',
            'subject' => 'nullable|string|max:190',
            'body' => 'required|string|max:20000',
            'letter_date' => 'required|date',
            'reference' => 'nullable|string|max:60',
        ]);
        $template = ! empty($data['letter_template_id']) ? LetterTemplate::find($data['letter_template_id']) : null;
        $letter = IssuedLetter::create($data + ['type' => $template?->type ?? 'other', 'issued_by' => $me->id]);
        if (empty($letter->reference)) {
            $letter->update(['reference' => 'HR/'.strtoupper(substr($letter->type, 0, 3)).'/'.LeaveService::fy($letter->letter_date).'/'.str_pad((string) $letter->id, 4, '0', STR_PAD_LEFT)]);
        }

        return redirect()->route('letters.index', ['user_id' => $letter->user_id])
            ->with('success', 'Letter issued. <a href="'.route('letters.pdf', $letter).'" target="_blank">Open PDF</a>');
    }

    public function pdf(Request $request, IssuedLetter $letter)
    {
        $me = $request->user();
        abort_unless((int) $letter->user_id === (int) $me->id || LeaveService::isHr($me), 403);
        $letter->load(['user.designation', 'user.businessUnit']);
        $pdf = Pdf::loadView('hr.letters.pdf', ['l' => $letter, 'u' => $letter->user, 'html' => LetterText::html($letter->body)])->setPaper('a4');
        $file = Str::slug(($letter->subject ?: 'letter').'-'.$letter->user->name).'.pdf';

        return $request->boolean('download') ? $pdf->download($file) : $pdf->stream($file);
    }

    public function destroy(Request $request, IssuedLetter $letter)
    {
        $this->hrOnly($request);
        $letter->delete();

        return back()->with('success', 'Letter removed.');
    }

    public function mine(Request $request)
    {
        $letters = IssuedLetter::where('user_id', $request->user()->id)->latest('letter_date')->get();

        return view('hr.letters.mine', compact('letters'));
    }

    private function hrOnly(Request $request): User
    {
        $me = $request->user();
        abort_unless(LeaveService::isHr($me), 403);

        return $me;
    }
}

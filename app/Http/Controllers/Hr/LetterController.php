<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\IssuedLetter;
use App\Models\LetterSignatory;
use App\Models\LetterTemplate;
use App\Models\User;
use App\Services\LeaveService;
use App\Support\LetterText;
use App\Support\SignatureImage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
        $signatories = Schema::hasTable('letter_signatories') ? LetterSignatory::orderByDesc('active')->orderByDesc('is_default')->orderBy('name')->get() : collect();

        return view('hr.letters.index', compact('templates', 'letters', 'people', 'signatories'));
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
            'body' => 'required|string|max:200000',
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
        $signatories = Schema::hasTable('letter_signatories') ? LetterSignatory::where('active', true)->orderBy('name')->get() : collect();
        $suggested = $person ? LetterSignatory::forCompany(self::companyOf($person)) : null;

        return view('hr.letters.create', compact('templates', 'people', 'person', 'template', 'filled', 'blanks', 'signatories', 'suggested'));
    }

    public function store(Request $request)
    {
        $me = $this->hrOnly($request);
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'letter_template_id' => 'nullable|exists:letter_templates,id',
            'subject' => 'nullable|string|max:190',
            'body' => 'required|string|max:200000',
            'letter_date' => 'required|date',
            'reference' => 'nullable|string|max:60',
            'signatory' => 'nullable|string|max:20', // signatory id, or "none"
        ]);
        $sign = $data['signatory'] ?? null;
        unset($data['signatory']);
        if (Schema::hasColumn('issued_letters', 'letter_signatory_id')) {
            $data['no_signature'] = $sign === 'none';
            $data['letter_signatory_id'] = ctype_digit((string) $sign) ? (int) $sign : null;
        }
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
        $file = Str::slug(($letter->subject ?: 'letter').'-'.$letter->user->name).'.pdf';

        // A copy signed with the DSC token (uploaded back by HR) wins over the generated one, unless HR asks for the unsigned copy.
        if ($letter->signed_pdf && ! $request->boolean('unsigned') && Storage::disk('public')->exists($letter->signed_pdf)) {
            return Storage::disk('public')->response($letter->signed_pdf, $file, ['Content-Type' => 'application/pdf'],
                $request->boolean('download') ? 'attachment' : 'inline');
        }

        $pdf = $this->buildPdf($letter, $request->boolean('unsigned'));

        return $request->boolean('download') ? $pdf->download($file) : $pdf->stream($file);
    }

    /** Upload the copy signed outside the app (USB token / DSC), or remove it. */
    /**
     * The app's PDF of a letter. With a signature image chosen (or the company's default), it is printed;
     * with "DSC token" the space is left empty for signing on the computer.
     */
    private function buildPdf(IssuedLetter $letter, bool $unsigned = false)
    {
        $letter->loadMissing(['user.designation', 'user.businessUnit']);
        $signatory = null;
        if (! $letter->no_signature && ! $unsigned && Schema::hasTable('letter_signatories')) {
            $signatory = $letter->signatory ?? LetterSignatory::forCompany(self::companyOf($letter->user));
        }
        $company = self::companyOf($letter->user);
        $view = [
            'l' => $letter, 'u' => $letter->user, 'company' => $company,
            'signatory' => $signatory, 'signImg' => $signatory?->dataUri('signature'), 'stampImg' => $signatory?->dataUri('stamp'),
            'letterhead' => self::letterhead($company),
        ];
        // Blocks the template places itself: the "To" part, and the signature (image, or space for the DSC token).
        $blocks = [
            '[[to]]' => view('hr.letters._to', $view)->render(),
            '[[company-sign]]' => view('hr.letters._company_sign', $view)->render(),
        ];
        $view['html'] = LetterText::html($letter->body, $blocks);
        $view['ownTo'] = str_contains((string) $letter->body, '[[to]]');
        $view['ownSign'] = str_contains((string) $letter->body, '[[company-sign]]');

        return Pdf::loadView('hr.letters.pdf', $view)->setPaper('a4');
    }

    /** Email the letter (the token-signed copy if uploaded, else the app's PDF) to the employee. */
    public function email(Request $request, IssuedLetter $letter)
    {
        $me = $this->hrOnly($request);
        $letter->loadMissing('user');
        $u = $letter->user;
        $to = trim((string) $request->input('to')) ?: ($u->official_email ?: ($u->personal_email ?: $u->email));
        if (! $to || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return back()->with('error', 'No email address for '.$u->name.'. Add their official email on the profile first.');
        }
        $file = Str::slug(($letter->subject ?: 'letter').'-'.$u->name).'.pdf';
        if ($letter->signed_pdf && Storage::disk('public')->exists($letter->signed_pdf)) {
            $bytes = Storage::disk('public')->get($letter->signed_pdf);
        } else {
            $bytes = $this->buildPdf($letter)->output();
        }
        try {
            Mail::to($to)->send(new \App\Mail\LetterMail($letter, $bytes, $file, self::companyOf($u), $me));
        } catch (\Throwable $e) {
            return back()->with('error', 'The email could not be sent: '.$e->getMessage());
        }
        if (Schema::hasColumn('issued_letters', 'emailed_at')) {
            $letter->update(['emailed_at' => now(), 'emailed_to' => $to]);
        }

        return back()->with('success', 'Letter emailed to '.$u->name.' ('.$to.').');
    }

    public function uploadSigned(Request $request, IssuedLetter $letter)
    {
        $this->hrOnly($request);
        if ($request->boolean('remove')) {
            if ($letter->signed_pdf) {
                Storage::disk('public')->delete($letter->signed_pdf);
            }
            $letter->update(['signed_pdf' => null]);

            return back()->with('success', 'Signed copy removed. The letter shows the app copy again.');
        }
        $request->validate(['signed_pdf' => 'required|file|mimes:pdf|max:10240']);
        if ($letter->signed_pdf) {
            Storage::disk('public')->delete($letter->signed_pdf);
        }
        $letter->update(['signed_pdf' => $request->file('signed_pdf')->store('letters-signed', 'public')]);

        return back()->with('success', 'Signed copy saved. The person now sees the signed PDF.');
    }

    public function editSignatory(Request $request, ?LetterSignatory $signatory = null)
    {
        $this->hrOnly($request);
        $signatory ??= new LetterSignatory(['active' => true]);
        $companies = User::query()->whereNotNull('entity')->where('entity', '!=', '')->distinct()->orderBy('entity')->pluck('entity');

        return view('hr.letters.signatory', compact('signatory', 'companies'));
    }

    public function saveSignatory(Request $request, ?LetterSignatory $signatory = null)
    {
        $this->hrOnly($request);
        $signatory ??= new LetterSignatory;
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'designation' => 'nullable|string|max:120',
            'company' => 'nullable|string|max:190',
            'signature' => ($signatory->exists ? 'nullable' : 'required').'|image|max:5120',
            'stamp' => 'nullable|image|max:5120',
        ]);
        foreach (['signature', 'stamp'] as $f) {
            unset($data[$f]);
            if ($request->hasFile($f)) {
                if ($signatory->{$f}) {
                    Storage::disk('public')->delete($signatory->{$f});
                }
                $data[$f] = SignatureImage::store($request->file($f));
            } elseif ($f === 'stamp' && $request->boolean('remove_stamp') && $signatory->stamp) {
                Storage::disk('public')->delete($signatory->stamp);
                $data['stamp'] = null;
            }
        }
        $data['active'] = $request->boolean('active');
        $data['is_default'] = $request->boolean('is_default');
        $signatory->fill($data)->save();
        if ($signatory->is_default) {
            LetterSignatory::where('id', '!=', $signatory->id)->update(['is_default' => false]);
        }

        return redirect()->route('letters.index')->with('success', 'Signature saved. New letters will carry it.');
    }

    public function deleteSignatory(Request $request, LetterSignatory $signatory)
    {
        $this->hrOnly($request);
        // Letters already issued keep looking the same, so only hide it.
        $signatory->update(['active' => false, 'is_default' => false]);

        return back()->with('success', 'Signature turned off for new letters.');
    }

    /** Printed letterhead (header and footer images) for companies that have one. */
    private static function letterhead(string $company): ?array
    {
        if (! \App\Services\CelebrationService::isKeystone($company)) {
            return null;
        }
        $img = fn ($f) => is_file($p = public_path('assets/letters/'.$f)) ? 'data:image/jpeg;base64,'.base64_encode(file_get_contents($p)) : null;
        $head = $img('keystone-header.jpg');
        $foot = $img('keystone-footer.jpg');

        return $head && $foot ? ['header' => $head, 'footer' => $foot] : null;
    }

    private static function companyOf(User $u): string
    {
        $u->loadMissing('businessUnit');

        return (string) ($u->entity ?: ($u->businessUnit?->name ?? config('app.name')));
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

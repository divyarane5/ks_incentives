<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;

use App\Models\Lead;
use App\Models\LeadCaptureForm;
use App\Models\Project;
use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Public endpoints for website enquiries: a ready-made form page, and a link that any
 * website form or code can post to. No login; each form is identified by its secret token.
 */
class CaptureController extends Controller
{
    public function __construct(private LeadService $leads) {}

    public function form(string $token)
    {
        $form = $this->find($token);

        return view('crm.capture.form', ['form' => $form]);
    }

    public function thanks(string $token)
    {
        return view('crm.capture.thanks', ['form' => $this->find($token)]);
    }

    public function store(Request $request, string $token)
    {
        $form = $this->find($token);
        $json = $request->expectsJson() || $request->isJson();
        // Errors from other websites always come back as JSON; the ready-made form page shows them on the page.
        $api = $request->is('api/*');

        // Bots fill in every field, including this hidden one. Pretend it worked.
        if (filled($request->input('company_website'))) {
            return $this->done($form, $json);
        }

        $this->acceptOtherFieldNames($request);
        $phone = Lead::normalizePhone((string) $request->input('phone', ''));
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
            'project' => ['nullable', 'string', 'max:150'],
            'sub_source' => ['nullable', 'string', 'max:100'],
            'utm_source' => ['nullable', 'string', 'max:100'],
            'utm_campaign' => ['nullable', 'string', 'max:150'],
            'page' => ['nullable', 'string', 'max:500'],
        ]);
        if (strlen($phone) < 10 || strlen($phone) > 15) {
            return ($json || $api)
                ? response()->json(['ok' => false, 'errors' => ['phone' => ['Please enter a valid mobile number.']]], 422)
                : back()->withErrors(['phone' => 'Please enter a valid mobile number.'])->withInput();
        }

        $projectIds = array_filter([$form->project_id]);
        if (! $projectIds && ! empty($data['project'])) {
            $projectIds = Project::active()->where('name', $data['project'])->pluck('id')->all();
        }

        $notes = trim(implode("\n", array_filter([
            $data['message'] ?? null,
            ! empty($data['page']) ? 'Page: '.$data['page'] : null,
            empty($projectIds) && ! empty($data['project']) ? 'Project asked about: '.$data['project'] : null,
        ])));

        $this->leads->createOrReEnquire([
            'name' => Str::squish($data['name'] ?? '') ?: 'Website enquiry',
            'phone' => $phone,
            'email' => isset($data['email']) ? strtolower($data['email']) : null,
            'source_id' => $form->source_id,
            'sub_source' => Str::limit($data['sub_source'] ?? $data['utm_source'] ?? $form->name, 100, ''),
            'campaign' => $data['utm_campaign'] ?? null,
            'owner_id' => $form->owner_id,
            'notes' => $notes ?: null,
        ], ['enquired_for' => 'buy'], $projectIds, null);

        $form->increment('leads_count');
        $form->forceFill(['last_used_at' => now()])->save();

        return $this->done($form, $json);
    }

    /**
     * Portals (99acres, MagicBricks, Housing) and website builders name fields differently.
     * Copy the common alternatives onto our names so they work without changes on their side.
     */
    private function acceptOtherFieldNames(Request $request): void
    {
        $aliases = [
            'phone' => ['mobile', 'mobile_no', 'mobile_number', 'phone_number', 'contact', 'contact_no', 'contact_number', 'Phone', 'Mobile'],
            'name' => ['full_name', 'fullname', 'customer_name', 'contact_name', 'Name'],
            'email' => ['email_id', 'emailid', 'Email'],
            'message' => ['query', 'comments', 'comment', 'remarks', 'enquiry', 'requirement', 'Message'],
            'project' => ['project_name', 'property', 'property_name', 'Project'],
            'sub_source' => ['subsource', 'Sub Source'],
        ];
        $fill = [];
        foreach ($aliases as $ours => $theirs) {
            if (blank($request->input($ours))) {
                foreach ($theirs as $alt) {
                    if (filled($request->input($alt))) {
                        $fill[$ours] = (string) $request->input($alt);
                        break;
                    }
                }
            }
        }
        $request->merge($fill);
    }

    private function done(LeadCaptureForm $form, bool $json)
    {
        if ($json) {
            return response()->json(['ok' => true, 'message' => 'Thank you. We will call you shortly.']);
        }

        return $form->thank_you_url
            ? redirect()->away($form->thank_you_url)
            : redirect()->route('crm.capture.thanks', $form->token);
    }

    private function find(string $token): LeadCaptureForm
    {
        return LeadCaptureForm::where('token', $token)->where('is_active', true)->firstOrFail();
    }
}

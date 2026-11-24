<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;

use App\Models\LeadCaptureForm;
use App\Models\LeadSource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Admin screen for website enquiry forms. */
class LeadCaptureFormController extends Controller
{
    public function index()
    {
        return view('crm.capture-forms.index', [
            'forms' => LeadCaptureForm::with(['source', 'project', 'owner'])->latest()->get(),
        ]);
    }

    public function create()
    {
        return view('crm.capture-forms.form', $this->formData() + [
            'form' => new LeadCaptureForm([
                'name' => 'Main website',
                'source_id' => LeadSource::where('slug', 'website')->value('id'),
                'is_active' => true,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $form = LeadCaptureForm::create($this->validated($request));

        return redirect()->route('crm.capture-forms.edit', $form)->with('status', 'Form created. Share the link or add it to your website below.');
    }

    public function edit(LeadCaptureForm $captureForm)
    {
        return view('crm.capture-forms.form', $this->formData() + ['form' => $captureForm]);
    }

    public function update(Request $request, LeadCaptureForm $captureForm)
    {
        $captureForm->update($this->validated($request));

        return redirect()->route('crm.capture-forms.edit', $captureForm)->with('status', 'Saved.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'heading' => ['nullable', 'string', 'max:150'],
            'source_id' => ['required', Rule::exists('lead_sources', 'id')],
            'project_id' => ['nullable', Rule::exists('projects', 'id')],
            'owner_id' => ['nullable', Rule::exists('users', 'id')],
            'thank_you_url' => ['nullable', 'url:https,http', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        return $data;
    }

    private function formData(): array
    {
        return [
            'sources' => LeadSource::where('is_active', true)->orderBy('sort_order')->get(['id', 'name']),
            'projects' => Project::active()->orderBy('name')->get(['id', 'name']),
            'owners' => User::active()->crmUsers()->orderBy('name')->get(['id', 'name']),
        ];
    }
}

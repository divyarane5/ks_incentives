<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;

use App\Models\AssignmentRule;
use App\Models\LeadSource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Admin screen for auto-assignment rules. */
class AssignmentRuleController extends Controller
{
    public function index()
    {
        return view('crm.assignment-rules.index', [
            'rules' => AssignmentRule::with(['source', 'project'])->orderBy('sort_order')->orderBy('id')->get(),
            'names' => User::pluck('name', 'id'),
        ]);
    }

    public function create()
    {
        return view('crm.assignment-rules.form', $this->formData() + ['rule' => new AssignmentRule(['is_active' => true, 'user_ids' => []])]);
    }

    public function store(Request $request)
    {
        AssignmentRule::create($this->validated($request));

        return redirect()->route('crm.assignment-rules.index')->with('status', 'Rule added.');
    }

    public function edit(AssignmentRule $assignmentRule)
    {
        return view('crm.assignment-rules.form', $this->formData() + ['rule' => $assignmentRule]);
    }

    public function update(Request $request, AssignmentRule $assignmentRule)
    {
        $assignmentRule->update($this->validated($request));

        return redirect()->route('crm.assignment-rules.index')->with('status', 'Rule saved.');
    }

    public function destroy(AssignmentRule $assignmentRule)
    {
        $assignmentRule->delete();

        return redirect()->route('crm.assignment-rules.index')->with('status', 'Rule deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'source_id' => ['nullable', Rule::exists('lead_sources', 'id')],
            'project_id' => ['nullable', Rule::exists('projects', 'id')],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', Rule::exists('users', 'id')],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ], ['user_ids.required' => 'Tick at least one person.']);
        $data['user_ids'] = array_values(array_map('intval', $data['user_ids']));
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }

    private function formData(): array
    {
        return [
            'sources' => LeadSource::where('is_active', true)->orderBy('sort_order')->get(['id', 'name']),
            'projects' => Project::active()->orderBy('name')->get(['id', 'name']),
            'people' => User::active()->crmUsers([User::ROLE_SALES, User::ROLE_MANAGER])->orderBy('name')->get(['id', 'name']),
        ];
    }
}

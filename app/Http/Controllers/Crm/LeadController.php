<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Models\Project;
use App\Models\PropertyType;
use App\Models\User;
use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    public const TABS = [
        'mine' => 'My leads',
        'today' => 'Today',
        'overdue' => 'Overdue',
        'team' => 'Team',
        'unassigned' => 'Unassigned',
        'closed' => 'Closed',
        'all' => 'All',
    ];

    public function __construct(private LeadService $leads) {}

    public function index(Request $request)
    {
        $user = $request->user();
        // Sales open on their own leads; managers and admins on their whole team.
        $default = $user->role === User::ROLE_SALES ? 'mine' : 'team';
        $tab = array_key_exists($request->query('tab'), self::TABS) ? $request->query('tab') : $default;

        $query = Lead::query()->visibleTo($user)->with(['stage', 'subStage', 'source', 'owner:id,name']);

        match ($tab) {
            'mine' => $query->active()->where(fn ($q) => $q->where('owner_id', $user->id)->orWhere('secondary_owner_id', $user->id)),
            'today' => $query->active()->where('owner_id', $user->id)->whereBetween('next_follow_up_at', [now()->startOfDay(), now()->endOfDay()]),
            'overdue' => $query->active()->where('next_follow_up_at', '<', now()),
            'team' => $query->active(),
            'unassigned' => $query->active()->whereNull('owner_id'),
            'closed' => $query->where(fn ($q) => $q->where('is_archived', true)->orWhereIn('stage_id', LeadStage::where('outcome', 'won')->pluck('id'))),
            'all' => $query,
        };

        if ($search = trim((string) $request->query('q'))) {
            $digits = preg_replace('/\D+/', '', $search);
            $query->where(function ($q) use ($search, $digits) {
                $q->where('name', 'like', "%{$search}%")->orWhere('serial_no', $search);
                if (strlen($digits) >= 4) {
                    $q->orWhere('phone', 'like', '%'.Lead::normalizePhone($digits).'%');
                }
            });
        }

        $query->when($request->query('stage'), fn ($q, $v) => $q->where('stage_id', $v))
            ->when($request->query('source'), fn ($q, $v) => $v === 'none' ? $q->whereNull('source_id') : $q->where('source_id', $v))
            ->when($request->query('sub_source'), fn ($q, $v) => $v === 'none' ? $q->where(fn ($w) => $w->whereNull('sub_source')->orWhere('sub_source', '')) : $q->where('sub_source', $v))
            ->when($request->query('owner'), fn ($q, $v) => $v === 'none' ? $q->whereNull('owner_id') : $q->where('owner_id', $v))
            ->when($request->query('temperature'), fn ($q, $v) => $q->where('temperature', $v))
            ->when($request->query('project'), fn ($q, $v) => $q->whereHas('projects', fn ($p) => $p->where('projects.id', $v)))
            ->when($request->query('from'), fn ($q, $v) => $q->where('created_at', '>=', $v))
            ->when($request->query('to'), fn ($q, $v) => $q->where('created_at', '<=', $v.' 23:59:59'));

        $sort = in_array($tab, ['today', 'overdue'], true) ? 'next_follow_up_at' : 'updated_at';
        $leads = $query->orderBy($sort, $sort === 'next_follow_up_at' ? 'asc' : 'desc')->paginate(25)->withQueryString();

        return view('crm.leads.index', [
            'leads' => $leads,
            'tab' => $tab,
            'tabs' => self::TABS,
            'stages' => LeadStage::whereNull('parent_id')->orderBy('sort_order')->get(),
            'sources' => LeadSource::with('subSources')->where('is_active', true)->orderBy('sort_order')->get(),
            'projects' => Project::active()->orderBy('name')->get(['id', 'name']),
            'owners' => $this->assignableUsers($user),
            'canAssign' => $user->can('assign', Lead::class),
        ]);
    }

    public function create(Request $request)
    {
        return view('crm.leads.form', $this->formData($request->user()) + ['lead' => new Lead]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        [$data, $enquiry, $projectIds] = $this->validated($request, $user);

        [$lead, $created] = $this->leads->createOrReEnquire($data, $enquiry, $projectIds, $user);

        if (! $created) {
            if ($user->cannot('view', $lead)) {
                return redirect()->route('crm.leads.index')
                    ->with('warning', 'This phone number already belongs to a lead owned by '.($lead->owner?->name ?? 'another team').'. It has been recorded as a re-enquiry on that lead.');
            }

            return redirect()->route('crm.leads.show', $lead)
                ->with('warning', 'This phone number already exists. The enquiry was added to this lead as a re-enquiry.');
        }

        return redirect()->route('crm.leads.show', $lead)->with('status', 'Lead added.');
    }

    public function show(Request $request, Lead $lead)
    {
        $this->authorize('view', $lead);

        $lead->load(['stage', 'subStage', 'source', 'owner', 'secondaryOwner', 'enquiry.propertyType', 'projects',
            'activities.user:id,name', 'appointments.project', 'callLogs.user:id,name', 'originalLead']);

        return view('crm.leads.show', [
            'lead' => $lead,
            'stages' => LeadStage::with('children')->whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->get(),
            'projects' => Project::active()->orderBy('name')->get(['id', 'name']),
            'owners' => $this->assignableUsers($request->user()),
            'canAssign' => $request->user()->can('assign', Lead::class),
            'templates' => \App\Models\MessageTemplate::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function edit(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);
        $lead->load(['enquiry', 'projects']);

        return view('crm.leads.form', $this->formData($request->user()) + ['lead' => $lead]);
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);
        [$data, $enquiry, $projectIds] = $this->validated($request, $request->user(), $lead);
        unset($data['owner_id'], $data['notes']);

        $data['phone'] = Lead::normalizePhone($data['phone']);
        $lead->update($data);
        $lead->enquiry()->updateOrCreate([], $enquiry);
        $lead->projects()->sync($projectIds);
        $this->leads->log($lead, $request->user(), 'updated', null, null, 'Lead details edited');

        return redirect()->route('crm.leads.show', $lead)->with('status', 'Lead updated.');
    }

    public function destroy(Request $request, Lead $lead)
    {
        $this->authorize('delete', $lead);
        $lead->delete();

        return redirect()->route('crm.leads.index')->with('status', 'Lead deleted.');
    }

    /** @return array{0: array, 1: array, 2: array<int>} */
    private function validated(Request $request, User $user, ?Lead $lead = null): array
    {
        $v = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'min:8', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'alt_phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'source_id' => ['required', Rule::exists('lead_sources', 'id')],
            'sub_source' => ['nullable', 'string', 'max:100'],
            'temperature' => ['nullable', Rule::in(['hot', 'warm', 'cold'])],
            'owner_id' => ['nullable', Rule::exists('users', 'id')],
            'notes' => ['nullable', 'string', 'max:2000'],
            'enquired_for' => ['required', Rule::in(['buy', 'rent'])],
            'property_type_id' => ['nullable', Rule::exists('property_types', 'id')],
            'bhk' => ['nullable', 'numeric', 'between:0,20'],
            'budget_min' => ['nullable', 'integer', 'min:0'],
            'budget_max' => ['nullable', 'integer', 'min:0', 'gte:budget_min'],
            'locations' => ['nullable', 'string', 'max:500'],
            'purpose' => ['nullable', Rule::in(['investment', 'self_use'])],
            'project_ids' => ['nullable', 'array'],
            'project_ids.*' => [Rule::exists('projects', 'id')],
        ]);

        // Sales people can only add leads for themselves.
        if ($user->role === User::ROLE_SALES || empty($v['owner_id'])) {
            $v['owner_id'] = $user->id;
        }

        $data = collect($v)->only(['name', 'phone', 'alt_phone', 'email', 'source_id', 'sub_source', 'temperature', 'owner_id', 'notes'])->all();
        $enquiry = collect($v)->only(['enquired_for', 'property_type_id', 'bhk', 'budget_min', 'budget_max', 'locations', 'purpose'])->all();

        return [$data, $enquiry, $v['project_ids'] ?? []];
    }

    private function formData(User $user): array
    {
        return [
            'sources' => LeadSource::with('subSources')->where('is_active', true)->orderBy('sort_order')->get(),
            'propertyTypes' => PropertyType::with('children')->whereNull('parent_id')->orderBy('sort_order')->get(),
            'projects' => Project::active()->orderBy('name')->get(['id', 'name']),
            'owners' => $this->assignableUsers($user),
            'canAssign' => $user->can('assign', Lead::class),
        ];
    }

    private function assignableUsers(User $user)
    {
        return User::assignableBy($user);
    }
}

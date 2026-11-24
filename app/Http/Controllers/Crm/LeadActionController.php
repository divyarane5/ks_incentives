<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;

use App\Models\Appointment;
use App\Models\Lead;
use App\Models\LeadStage;
use App\Models\User;
use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/** Quick actions on a lead: stage, call outcome, note, assignment, appointments. */
class LeadActionController extends Controller
{
    public function __construct(private LeadService $leads) {}

    public function stage(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);
        $v = $request->validate([
            'stage_id' => ['required', Rule::exists('lead_stages', 'id')->whereNull('parent_id')],
            'sub_stage_id' => ['nullable', Rule::exists('lead_stages', 'id')],
            'follow_up_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:2000'],
            'project_id' => ['nullable', Rule::exists('projects', 'id')],
        ]);

        $stage = LeadStage::findOrFail($v['stage_id']);
        $sub = isset($v['sub_stage_id']) ? LeadStage::where('parent_id', $stage->id)->find($v['sub_stage_id']) : null;

        if ($stage->schedule_type && ! $stage->isClosed() && empty($v['follow_up_at'])) {
            return back()->withErrors(['follow_up_at' => 'Pick a date and time for the '.str_replace('_', ' ', $stage->schedule_type).'.'])->withInput();
        }
        if ($stage->children()->exists() && ! $sub) {
            return back()->withErrors(['sub_stage_id' => 'Pick a reason for '.$stage->name.'.'])->withInput();
        }

        $followUp = ! empty($v['follow_up_at']) ? Carbon::parse($v['follow_up_at']) : null;
        $this->leads->changeStage($lead, $stage, $sub, $followUp, $v['note'] ?? null, $request->user(), ['project_id' => $v['project_id'] ?? null]);

        return redirect()->route('crm.leads.show', $lead)->with('status', 'Updated to '.$stage->name.'.');
    }

    public function call(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);
        $v = $request->validate([
            'outcome' => ['required', Rule::in(['answered', 'busy', 'not_answered', 'not_reachable', 'switched_off', 'wrong_number'])],
            'notes' => ['nullable', 'string', 'max:2000'],
            'duration_minutes' => ['nullable', 'integer', 'min:0', 'max:600'],
        ]);

        $this->leads->logCall($lead, $v['outcome'], $v['notes'] ?? null,
            isset($v['duration_minutes']) ? $v['duration_minutes'] * 60 : null, $request->user());

        return redirect()->route('crm.leads.show', $lead)->with('status', 'Call logged. Now update the stage and next follow-up.');
    }

    public function note(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);
        $v = $request->validate(['note' => ['required', 'string', 'max:2000']]);
        $this->leads->addNote($lead, $v['note'], $request->user());

        return redirect()->route('crm.leads.show', $lead)->with('status', 'Note added.');
    }

    public function assign(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);
        $this->authorize('assign', Lead::class);
        [$owner, $secondary] = $this->assignees($request);

        $this->leads->assign([$lead], $owner, $secondary, $request->user());

        return redirect()->route('crm.leads.show', $lead)->with('status', 'Assigned to '.$owner->name.'.');
    }

    public function bulkAssign(Request $request)
    {
        $this->authorize('assign', Lead::class);
        $request->validate(['lead_ids' => ['required', 'array', 'max:500'], 'lead_ids.*' => ['integer']]);
        [$owner, $secondary] = $this->assignees($request);

        $leads = Lead::visibleTo($request->user())->with('owner')->whereIn('id', $request->input('lead_ids'))->get();
        $count = $this->leads->assign($leads, $owner, $secondary, $request->user());

        return back()->with('status', $count.' lead(s) assigned to '.$owner->name.'.');
    }

    public function appointment(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment->lead);
        $v = $request->validate(['status' => ['required', Rule::in(['done', 'not_done', 'cancelled'])]]);
        $this->leads->markAppointment($appointment, $v['status'], $request->user());

        return redirect()->route('crm.leads.show', $appointment->lead)->with('status', 'Updated.');
    }

    /** @return array{0: User, 1: ?User} */
    private function assignees(Request $request): array
    {
        $v = $request->validate([
            'owner_id' => ['required', Rule::exists('users', 'id')->where('status', 'Active')],
            'secondary_owner_id' => ['nullable', Rule::exists('users', 'id')->where('status', 'Active')],
        ]);
        $user = $request->user();
        $allowed = $user->isAdmin() ? null : $user->teamUserIds();
        foreach (['owner_id', 'secondary_owner_id'] as $key) {
            if (! empty($v[$key]) && $allowed !== null && ! in_array((int) $v[$key], $allowed, true)) {
                abort(403, 'You can only assign leads within your team.');
            }
        }

        return [User::findOrFail($v['owner_id']), ! empty($v['secondary_owner_id']) ? User::find($v['secondary_owner_id']) : null];
    }
}

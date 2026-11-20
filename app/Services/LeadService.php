<?php

namespace App\Services;

use App\Models\CallLog;
use App\Models\Lead;
use App\Models\LeadStage;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * All changes to a lead go through here so every one of them lands on the lead's timeline.
 */
class LeadService
{
    /**
     * Create a lead, or, if the phone number already exists, record a re-enquiry on the existing lead.
     *
     * @return array{0: Lead, 1: bool} the lead and whether it was newly created
     */
    public function createOrReEnquire(array $data, array $enquiry, array $projectIds, ?User $by): array
    {
        return DB::transaction(function () use ($data, $enquiry, $projectIds, $by) {
            $existing = Lead::findByPhone($data['phone']);

            if ($existing) {
                $existing->increment('re_enquiry_count');
                $existing->forceFill(['re_enquired_at' => now()])->save();
                if ($projectIds) {
                    $existing->projects()->syncWithoutDetaching($projectIds);
                }
                $this->log($existing, $by, 're_enquiry', null, null, $data['notes'] ?? null, [
                    'source_id' => $data['source_id'] ?? null,
                    'sub_source' => $data['sub_source'] ?? null,
                ]);

                return [$existing, false];
            }

            $notes = $data['notes'] ?? null;
            unset($data['notes']);

            // No user = the lead came in by itself (website form); it stays unassigned unless an owner is given.
            $ownerId = array_key_exists('owner_id', $data) ? $data['owner_id'] : $by?->id;
            $lead = Lead::create(['owner_id' => $ownerId] + $data + [
                'stage_id' => LeadStage::where('is_default', true)->value('id'),
                'created_by_id' => $by?->id,
                'assigned_at' => $ownerId ? now() : null,
                'assigned_by_id' => $ownerId ? $by?->id : null,
            ]);
            $lead->enquiry()->create($enquiry);
            if ($projectIds) {
                $lead->projects()->sync($projectIds);
            }
            $this->log($lead, $by, 'created', null, null, $notes);
            if ($ownerId) {
                $this->notifyAssigned($lead, $ownerId, $by);
            } else {
                app(AutoAssigner::class)->assign($lead);
            }

            return [$lead, true];
        });
    }

    /**
     * Move a lead to a stage (and sub-stage), optionally with the next follow-up and a note.
     * Closed stages clear the follow-up and archive the lead.
     */
    public function changeStage(Lead $lead, LeadStage $stage, ?LeadStage $subStage, ?Carbon $followUpAt, ?string $note, User $by, array $appointment = []): void
    {
        DB::transaction(function () use ($lead, $stage, $subStage, $followUpAt, $note, $by, $appointment) {
            $from = $this->stageLabel($lead->stage, $lead->subStage);
            $to = $this->stageLabel($stage, $subStage);

            $lead->stage_id = $stage->id;
            $lead->sub_stage_id = $subStage?->id;
            $lead->picked_at ??= now();

            if ($stage->isClosed()) {
                $lead->next_follow_up_at = null;
                $lead->next_follow_up_type = null;
                $lead->is_archived = in_array($stage->outcome, ['lost', 'cancelled'], true);
            } else {
                $lead->is_archived = false;
                $lead->next_follow_up_at = $followUpAt;
                $lead->next_follow_up_type = $followUpAt ? ($stage->schedule_type ?? 'callback') : null;
            }
            $lead->save();

            if ($from !== $to) {
                $this->log($lead, $by, 'stage_changed', $from, $to, $note, $followUpAt ? ['follow_up_at' => $followUpAt->toIso8601String()] : null);
            } elseif ($note || $followUpAt) {
                $this->log($lead, $by, $followUpAt ? 'follow_up_set' : 'note', null, $followUpAt?->format('d M Y, h:i A'), $note);
            }

            // Booked: open a booking in the back office (once per lead).
            if ($stage->slug === BookingFromLead::STAGE_SLUG && ! $lead->booking()->exists()) {
                $booking = app(BookingFromLead::class)->create($lead, $appointment['project_id'] ?? null);
                $this->log($lead, $by, 'note', null, null, 'Booking #'.$booking->id.' opened in the back office for approval.');
            }

            if (in_array($stage->schedule_type, ['meeting', 'site_visit'], true) && $followUpAt) {
                $lead->appointments()->create([
                    'type' => $stage->schedule_type,
                    'mode' => $subStage ? \Illuminate\Support\Str::snake($subStage->name) : null,
                    'project_id' => $appointment['project_id'] ?? null,
                    'scheduled_at' => $followUpAt,
                    'assigned_to_id' => $lead->owner_id,
                ]);
            }
        });
    }

    public function addNote(Lead $lead, string $note, User $by): void
    {
        $this->log($lead, $by, 'note', null, null, $note);
    }

    /** Log the outcome of a call made from the CRM. */
    public function logCall(Lead $lead, string $outcome, ?string $notes, ?int $durationSeconds, User $by): CallLog
    {
        return DB::transaction(function () use ($lead, $outcome, $notes, $durationSeconds, $by) {
            $call = $lead->callLogs()->create([
                'user_id' => $by->id,
                'outcome' => $outcome,
                'notes' => $notes,
                'duration_seconds' => $durationSeconds,
                'called_at' => now(),
            ]);
            $lead->forceFill(['last_contacted_at' => now(), 'picked_at' => $lead->picked_at ?? now()])->save();
            $this->log($lead, $by, 'call', null, str_replace('_', ' ', $outcome), $notes);

            return $call;
        });
    }

    /** Assign one or many leads to a new owner (and optionally a secondary owner). */
    public function assign(iterable $leads, User $owner, ?User $secondary, User $by): int
    {
        $count = 0;
        DB::transaction(function () use ($leads, $owner, $secondary, $by, &$count) {
            foreach ($leads as $lead) {
                $from = $lead->owner?->name ?? 'Unassigned';
                $lead->forceFill([
                    'owner_id' => $owner->id,
                    'secondary_owner_id' => $secondary?->id ?? $lead->secondary_owner_id,
                    'assigned_by_id' => $by->id,
                    'assigned_at' => now(),
                    'picked_at' => null,
                ])->save();
                $this->log($lead, $by, 'assigned', $from, $owner->name, null, $secondary ? ['secondary' => $secondary->name] : null);
                $this->notifyAssigned($lead, $owner->id, $by);
                $count++;
            }
        });

        return $count;
    }

    public function markAppointment(\App\Models\Appointment $appointment, string $status, User $by): void
    {
        $appointment->update([
            'status' => $status,
            'done_by_id' => $status === 'done' ? $by->id : null,
            'done_at' => $status === 'done' ? now() : null,
        ]);
        $label = $appointment->type === 'site_visit' ? 'Site visit' : 'Meeting';
        $this->log($appointment->lead, $by, 'appointment', null, $label.' '.str_replace('_', ' ', $status));
    }

    /** Tell someone a lead was given to them (not when they gave it to themselves). */
    public function notifyAssigned(Lead $lead, int $ownerId, ?User $by): void
    {
        if ($by && $by->id === $ownerId) {
            return;
        }
        User::find($ownerId)?->notify(new \App\Notifications\LeadAlert(
            'New lead: '.$lead->name,
            ($by ? 'Assigned by '.$by->name : 'Assigned automatically').' · '.($lead->source?->name ?? 'No source'),
            route('crm.leads.show', $lead),
            'assigned',
        ));
    }

    public function log(Lead $lead, ?User $by, string $type, ?string $from = null, ?string $to = null, ?string $body = null, ?array $meta = null): void
    {
        $lead->activities()->create([
            'user_id' => $by?->id,
            'type' => $type,
            'from_value' => $from,
            'to_value' => $to,
            'body' => $body,
            'meta' => $meta,
        ]);
    }

    private function stageLabel(?LeadStage $stage, ?LeadStage $sub): string
    {
        return trim(($stage?->name ?? '').($sub ? ' > '.$sub->name : ''));
    }
}

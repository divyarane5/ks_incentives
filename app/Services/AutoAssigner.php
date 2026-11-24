<?php

namespace App\Services;

use App\Models\AssignmentRule;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Gives a new, unassigned lead to the next person in turn (round robin), using the first
 * active rule that matches the lead's source and project. Leads that match no rule stay unassigned.
 */
class AutoAssigner
{
    /** @return bool whether the lead was assigned */
    public function assign(Lead $lead, bool $notify = true): bool
    {
        if ($lead->owner_id) {
            return false;
        }
        $projectIds = $lead->projects()->pluck('projects.id')->all();

        $rule = AssignmentRule::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get()
            ->first(fn (AssignmentRule $r) => (! $r->source_id || $r->source_id === $lead->source_id)
                && (! $r->project_id || in_array($r->project_id, $projectIds, true)));
        if (! $rule) {
            return false;
        }

        return DB::transaction(function () use ($rule, $lead, $notify) {
            // Lock the rule so two leads arriving together don't go to the same person.
            $rule = AssignmentRule::whereKey($rule->id)->lockForUpdate()->first();
            $pool = User::whereIn('id', $rule->user_ids ?? [])->active()->orderBy('id')->pluck('id')->all();
            if (! $pool) {
                return false;
            }
            $next = $pool[0];
            foreach ($pool as $id) {
                if ($rule->last_user_id && $id > $rule->last_user_id) {
                    $next = $id;
                    break;
                }
            }

            $lead->forceFill(['owner_id' => $next, 'assigned_at' => now(), 'assigned_by_id' => null])->save();
            $rule->forceFill(['last_user_id' => $next, 'assigned_count' => $rule->assigned_count + 1])->save();
            $lead->activities()->create([
                'type' => 'assigned',
                'from_value' => 'Unassigned',
                'to_value' => User::whereKey($next)->value('name'),
                'body' => 'Auto-assigned by rule "'.$rule->name.'"',
            ]);

            if ($notify) {
                app(LeadService::class)->notifyAssigned($lead, $next, null);
            }

            return true;
        });
    }
}

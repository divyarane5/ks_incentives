<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\User;

/** Overdue / due-today / not-yet-called counts for a person, and for a manager's team. */
class FollowUpSummary
{
    public function forUser(User $user): array
    {
        $mine = fn () => Lead::query()->active()->where('owner_id', $user->id);

        return [
            'overdue' => $mine()->where('next_follow_up_at', '<', now())->count(),
            'today' => $mine()->whereBetween('next_follow_up_at', [now(), now()->endOfDay()])->count(),
            'untouched' => $mine()->whereNull('picked_at')->count(),
        ];
    }

    /** Overdue count per person below this manager, worst first. @return array<int, array{user: User, overdue: int}> */
    public function teamOverdue(User $manager): array
    {
        if ($manager->role === User::ROLE_SALES) {
            return [];
        }
        $ids = $manager->isAdmin() ? User::active()->crmUsers()->pluck('id')->all() : $manager->teamUserIds();
        $ids = array_values(array_diff($ids, [$manager->id]));

        $counts = Lead::query()->active()->whereIn('owner_id', $ids)->where('next_follow_up_at', '<', now())
            ->selectRaw('owner_id, count(*) as total')->groupBy('owner_id')->orderByDesc('total')->toBase()->pluck('total', 'owner_id');
        $users = User::whereIn('id', $counts->keys())->get()->keyBy('id');

        return $counts->map(fn ($n, $id) => ['user' => $users[$id], 'overdue' => (int) $n])->values()->all();
    }
}

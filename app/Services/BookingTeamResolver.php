<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Works out a booking's TL, Sr. TL and Cluster Head by walking up the sales person's
 * reporting chain as it stood on the booking date (reporting-manager history first,
 * today's manager when no history covers that date).
 */
class BookingTeamResolver
{
    public const ROLE_COLUMNS = [
        'TL' => 'tl_user_id',
        'Sr. TL' => 'sr_tl_user_id',
        'CH' => 'ch_user_id',
    ];

    private array $roleCache = [];

    /** @return array{tl_user_id: ?int, sr_tl_user_id: ?int, ch_user_id: ?int} */
    public function teamFor(int $salesUserId, $bookingDate): array
    {
        $team = ['tl_user_id' => null, 'sr_tl_user_id' => null, 'ch_user_id' => null];
        $date = $bookingDate ? substr((string) $bookingDate, 0, 10) : now()->toDateString();

        $seen = [$salesUserId => true];
        $current = $salesUserId;

        for ($step = 0; $step < 10; $step++) {
            $manager = $this->managerOn($current, $date);
            if (! $manager || isset($seen[$manager])) {
                break;
            }
            $seen[$manager] = true;

            $roles = $this->rolesOf($manager);
            if (array_intersect($roles, ['Admin', 'Superadmin'])) {
                break; // top of the sales chain
            }
            foreach (self::ROLE_COLUMNS as $role => $column) {
                if (in_array($role, $roles, true) && $team[$column] === null) {
                    $team[$column] = $manager;
                }
            }
            $current = $manager;
        }

        return $team;
    }

    public function managerOn(int $userId, string $date): ?int
    {
        $fromHistory = DB::table('employee_reporting_manager_histories')
            ->where('user_id', $userId)
            ->whereDate('effective_from', '<=', $date)
            ->where(fn ($q) => $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', $date))
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->value('reporting_manager_id');

        if ($fromHistory) {
            return (int) $fromHistory;
        }

        $current = DB::table('users')->where('id', $userId)->value('reporting_manager_id');

        return $current ? (int) $current : null;
    }

    private function rolesOf(int $userId): array
    {
        return $this->roleCache[$userId] ??= DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_id', $userId)
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->pluck('roles.name')->all();
    }
}

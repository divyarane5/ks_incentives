<?php

namespace App\Services;

use App\Models\Holiday;
use App\Models\LeaveAdjustment;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

/** Leave rules for KeyPeople. Leave year = financial year (1 April to 31 March). */
class LeaveService
{
    /** "2026-27" for a date (default today). */
    public static function fy($date = null): string
    {
        $d = $date ? Carbon::parse($date) : now();
        $start = $d->month >= 4 ? $d->year : $d->year - 1;

        return $start.'-'.substr((string) ($start + 1), 2);
    }

    /** [start, end] dates of a financial year like "2026-27". */
    public static function fyRange(string $fy): array
    {
        $start = (int) substr($fy, 0, 4);

        return [Carbon::create($start, 4, 1)->startOfDay(), Carbon::create($start + 1, 3, 31)->endOfDay()];
    }

    /** People in charge of HR: KeyPeople module plus user-edit. */
    public static function isHr(?User $user): bool
    {
        return $user && $user->can('module-keypeople') && $user->can('user-edit');
    }

    /** Who approves this person's leave: their current manager (if still working), else HR. */
    public static function approverFor(User $user): ?int
    {
        $managerId = $user->reporting_manager_id;
        if (! $managerId || (int) $managerId === (int) $user->id) {
            return null;
        }
        $active = User::where('id', $managerId)->where('status', 'Active')->exists();

        return $active ? (int) $managerId : null;
    }

    public static function canDecide(?User $me, LeaveRequest $leave): bool
    {
        if (! $me || (int) $leave->user_id === (int) $me->id) {
            return false;
        }

        return (int) $leave->approver_id === (int) $me->id || self::isHr($me);
    }

    /** Weekly off day numbers (0 = Sunday) read from the free-text "work off" field, e.g. "Tuesday". */
    public static function weeklyOffs(User $user): array
    {
        $map = ['sun' => 0, 'mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6];
        $out = [];
        foreach ($map as $k => $n) {
            if (stripos((string) $user->work_off, $k) !== false) {
                $out[] = $n;
            }
        }

        return $out;
    }

    /** Holiday dates (Y-m-d => name) that apply to this person between two dates. */
    public static function holidays(User $user, $from, $to): array
    {
        return Holiday::whereBetween('date', [Carbon::parse($from)->toDateString(), Carbon::parse($to)->toDateString()])
            ->where(fn ($q) => $q->whereNull('location_id')->orWhere('location_id', $user->work_location_id))
            ->get()
            ->mapWithKeys(fn ($h) => [$h->date->toDateString() => $h->name])
            ->all();
    }

    /** Working days taken by a leave: weekly offs and holidays are not counted. */
    public static function countDays(User $user, $from, $to, ?string $half = null): float
    {
        $from = Carbon::parse($from)->startOfDay();
        $to = Carbon::parse($to)->startOfDay();
        if ($to->lt($from)) {
            return 0;
        }
        $offs = self::weeklyOffs($user);
        $hol = self::holidays($user, $from, $to);
        $days = 0;
        foreach (CarbonPeriod::create($from, $to) as $d) {
            if (in_array($d->dayOfWeek, $offs, true) || isset($hol[$d->toDateString()])) {
                continue;
            }
            $days++;
        }
        if ($half && $from->equalTo($to) && $days > 0) {
            return 0.5;
        }

        return (float) $days;
    }

    /** Yearly quota for this person in this FY, cut down for people who joined during the year. */
    public static function quota(User $user, LeaveType $type, string $fy): float
    {
        if (! $type->is_paid) {
            return 0;
        }
        [$start, $end] = self::fyRange($fy);
        $quota = (float) $type->yearly_quota;
        if ($user->joining_date) {
            $join = Carbon::parse($user->joining_date)->startOfMonth();
            if ($join->gt($end)) {
                return 0;
            }
            if ($join->gt($start)) {
                $months = (int) round(abs($join->diffInMonths($end->copy()->startOfMonth()))) + 1;
                $quota = floor($quota * $months / 12 * 2) / 2; // to the half day below
            }
        }

        return $quota;
    }

    /**
     * Balance per active leave type for one FY.
     * Each row: type, quota, adjusted, used, pending, available (null for unpaid types).
     */
    public static function balances(User $user, ?string $fy = null): array
    {
        $fy ??= self::fy();
        [$start, $end] = self::fyRange($fy);
        $types = LeaveType::active()->get();

        $taken = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'pending'])
            ->whereBetween('from_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('leave_type_id, status, SUM(days) as d')
            ->groupBy('leave_type_id', 'status')
            ->get();
        $adj = LeaveAdjustment::where('user_id', $user->id)->where('financial_year', $fy)
            ->selectRaw('leave_type_id, SUM(days) as d')->groupBy('leave_type_id')->pluck('d', 'leave_type_id');

        $rows = [];
        foreach ($types as $type) {
            $used = (float) $taken->where('leave_type_id', $type->id)->where('status', 'approved')->sum('d');
            $pending = (float) $taken->where('leave_type_id', $type->id)->where('status', 'pending')->sum('d');
            $quota = self::quota($user, $type, $fy);
            $adjusted = (float) ($adj[$type->id] ?? 0);
            $rows[] = [
                'type' => $type,
                'quota' => $quota,
                'adjusted' => $adjusted,
                'used' => $used,
                'pending' => $pending,
                'available' => $type->is_paid ? $quota + $adjusted - $used - $pending : null,
            ];
        }

        return $rows;
    }

    /** Approved unpaid-leave days per month for the salary page: ["2026-5" => 2.0, ...]. */
    public static function unpaidDaysByMonth(User $user, string $fy): array
    {
        [$start, $end] = self::fyRange($fy);
        $unpaid = LeaveType::where('is_paid', false)->pluck('id');
        if ($unpaid->isEmpty()) {
            return [];
        }
        $out = [];
        $leaves = LeaveRequest::where('user_id', $user->id)->where('status', 'approved')
            ->whereIn('leave_type_id', $unpaid)
            ->where('to_date', '>=', $start->toDateString())->where('from_date', '<=', $end->toDateString())
            ->get();
        foreach ($leaves as $l) {
            if ($l->half_day) {
                $k = $l->from_date->year.'-'.$l->from_date->month;
                $out[$k] = ($out[$k] ?? 0) + 0.5;
                continue;
            }
            $offs = self::weeklyOffs($user);
            $hol = self::holidays($user, $l->from_date, $l->to_date);
            foreach (CarbonPeriod::create($l->from_date, $l->to_date) as $d) {
                if ($d->lt($start) || $d->gt($end) || in_array($d->dayOfWeek, $offs, true) || isset($hol[$d->toDateString()])) {
                    continue;
                }
                $k = $d->year.'-'.$d->month;
                $out[$k] = ($out[$k] ?? 0) + 1;
            }
        }

        return $out;
    }
}

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

    /** HR dashboard: HR (KeyPeople + edit employees), Admin and Super Admin. Everyone else gets KeyMe. */
    public static function canSeeHrDashboard(?User $user): bool
    {
        return $user && (self::isHr($user) || $user->hasRole(config('roles.SUPER_ADMIN')) || $user->hasRole(config('roles.ADMIN')));
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
    /** Leave types that need 12 months of service (as in the appointment letter). */
    public const AFTER_A_YEAR = ['ML', 'PTL'];

    /** Paid leave (EL) can only be taken once confirmed, and at most this many days in a month. */
    public const PAID_LEAVE_CODE = 'EL';
    public const PAID_LEAVE_MONTH_CAP = 7;

    /** Maternity leave is for women, paternity leave for men; everything else is for everyone. */
    public static function eligible(User $user, LeaveType $type): bool
    {
        $g = strtolower((string) $user->gender);

        return match ($type->code) {
            'ML' => $g === 'female',
            'PTL' => $g === 'male',
            default => true,
        };
    }

    /** Active leave types this person can see and apply for. */
    public static function typesFor(User $user)
    {
        return LeaveType::active()->get()->filter(fn ($t) => self::eligible($user, $t))->values();
    }

    public static function isConfirmed(User $user, $on = null): bool
    {
        $on = $on ? Carbon::parse($on) : now();
        if (strtolower((string) $user->employment_status) === 'confirmed') {
            return true;
        }

        return $user->confirm_date && Carbon::parse($user->confirm_date)->lte($on);
    }

    /**
     * Rules from the appointment letter. Returns a message when the request breaks one, else null.
     * Paid leave only after confirmation, and at most 7 paid-leave days in one calendar month.
     */
    public static function ruleProblem(User $user, LeaveType $type, Carbon $from, Carbon $to, float $days): ?string
    {
        if (! self::eligible($user, $type)) {
            return "{$type->name} isn't available for this employee.";
        }
        if (in_array($type->code, self::AFTER_A_YEAR, true)
            && (! $user->joining_date || Carbon::parse($user->joining_date)->addMonths(12)->gt($from))) {
            return "{$type->name} can be taken after completing 12 months with the company.";
        }
        if ($type->code === self::PAID_LEAVE_CODE) {
            if (! self::isConfirmed($user, $from)) {
                return 'Paid leave can be taken only after confirmation. Use Casual / sick leave or Unpaid leave.';
            }
            // Days per month: this request's days are split across the months it covers.
            $offs = self::weeklyOffs($user);
            $holidays = self::holidays($user, $from, $to);
            $perMonth = [];
            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                $key = $d->format('Y-m');
                if (in_array($d->dayOfWeek, $offs, true) || isset($holidays[$d->toDateString()])) {
                    continue;
                }
                $perMonth[$key] = ($perMonth[$key] ?? 0) + 1;
            }
            if ($days < 1 && $perMonth) {
                $perMonth[array_key_first($perMonth)] = $days; // half day
            }
            foreach ($perMonth as $month => $n) {
                $mStart = Carbon::parse($month.'-01');
                $already = (float) LeaveRequest::where('user_id', $user->id)->where('leave_type_id', $type->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->whereBetween('from_date', [$mStart->toDateString(), $mStart->copy()->endOfMonth()->toDateString()])
                    ->sum('days');
                if ($already + $n > self::PAID_LEAVE_MONTH_CAP) {
                    return 'At most '.self::PAID_LEAVE_MONTH_CAP.' days of Paid leave in a month. '.$mStart->format('F').' already has '.rtrim(rtrim(number_format($already, 1), '0'), '.').'.';
                }
            }
        }

        return null;
    }

    public static function quota(User $user, LeaveType $type, string $fy): float
    {
        if (! $type->is_paid) {
            return 0;
        }
        [$start, $end] = self::fyRange($fy);
        $quota = (float) $type->yearly_quota;
        if (in_array($type->code, self::AFTER_A_YEAR, true)) {
            // Maternity / paternity: the full quota, once they have completed 12 months.
            if (! self::eligible($user, $type)) {
                return 0;
            }
            $joined = $user->joining_date ? Carbon::parse($user->joining_date) : null;

            return $joined && $joined->copy()->addMonths(12)->lte($end) ? $quota : 0;
        }
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
            if (! self::eligible($user, $type)) {
                continue;
            }
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

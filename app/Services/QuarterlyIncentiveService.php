<?php

namespace App\Services;

use App\Models\IncentivePayout;
use App\Models\IncentiveSlab;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Quarterly incentives worked out on the financial year so far.
 *
 * FOS:      times = collection so far / salary so far; the FOS slab for that "times" gives a %;
 *           incentive so far = (collection - salary x justification multiplier) x slab %.
 * Managers: team = bookings saved with them as TL / Sr. TL / CH at the time of booking;
 *           times = team collection / team salary; the highest role slab whose multiplier is
 *           reached gives a %; incentive so far = team FOS incentive x that %.
 * Payable this quarter = incentive so far - what earlier quarters of the year already froze.
 * People marked Exited are shown but get nothing. Admin and Super Admin are not included.
 *
 * Collection = brokerage received from the developer, before GST: bank amount + TDS,
 * dated by the bank receipt date (the invoice date when no receipt date was entered).
 */
class QuarterlyIncentiveService
{
    public const ROLES = ['FOS', 'TL', 'Sr. TL', 'CH'];

    public const MANAGER_COLUMNS = [
        'TL' => 'tl_user_id',
        'Sr. TL' => 'sr_tl_user_id',
        'CH' => 'ch_user_id',
    ];

    /** Months of the financial year in order, April first. */
    public const FY_MONTHS = [4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3];

    public static function quarterLabel(int $quarter): string
    {
        return ['', 'Q1 (Apr-Jun)', 'Q2 (Jul-Sep)', 'Q3 (Oct-Dec)', 'Q4 (Jan-Mar)'][$quarter] ?? '';
    }

    /** @return array{0: string, 1: string} first and last date of the year so far, up to the quarter's end */
    public static function period(string $fy, int $quarter): array
    {
        $startYear = (int) substr($fy, 0, 4);
        $start = sprintf('%d-04-01', $startYear);
        $end = [
            1 => sprintf('%d-06-30', $startYear),
            2 => sprintf('%d-09-30', $startYear),
            3 => sprintf('%d-12-31', $startYear),
            4 => sprintf('%d-03-31', $startYear + 1),
        ][$quarter];

        return [$start, $end];
    }

    public function isFrozen(string $fy, int $quarter): bool
    {
        return IncentivePayout::where('financial_year', $fy)->where('quarter', $quarter)->whereNotNull('frozen_at')->exists();
    }

    /**
     * Works out every row for the quarter. Nothing is saved.
     *
     * @return array{rows: Collection, warnings: array<string>}
     */
    public function calculate(string $fy, int $quarter): array
    {
        [$start, $end] = self::period($fy, $quarter);
        $warnings = [];

        $slabs = IncentiveSlab::where('financial_year', $fy)->get()->groupBy('role');
        if (! isset($slabs['FOS'])) {
            $warnings[] = "No FOS slabs are set for {$fy}, so nobody can be calculated. Add them under Incentives > Slabs.";
        }

        $salaries = $this->salariesSoFar($fy, $quarter);
        $collections = $this->collectionsSoFar($start, $end); // one row per booking
        $users = DB::table('users')->select('id', 'name', 'employee_code', 'status')->get()->keyBy('id');
        $frozenBefore = IncentivePayout::where('financial_year', $fy)->where('quarter', '<', $quarter)
            ->whereNotNull('frozen_at')
            ->selectRaw('user_id, role, SUM(payable) as total')->groupBy('user_id', 'role')->get()
            ->mapWithKeys(fn ($r) => [$r->user_id.'|'.$r->role => (float) $r->total]);

        $undated = DB::table('booking_brokerage_payments')->whereNull('deleted_at')
            ->where('bank_received_amount', '>', 0)->whereNull('bank_received_date')->count();
        if ($undated) {
            $warnings[] = "{$undated} payment entries have no bank receipt date, so their invoice date was used instead.";
        }

        $rows = collect();

        // ---------- FOS ----------
        $fosIncentive = []; // user_id => calculated incentive so far (before leaver rule)
        foreach ($collections->groupBy('fos_id') as $fosId => $items) {
            $collection = round($items->sum('amount'), 2);
            $salary = $salaries[$fosId] ?? 0.0;
            $row = $this->baseRow($users[$fosId] ?? null, (int) $fosId, 'FOS', $salary, $collection);
            $row['details'] = ['bookings' => $items->pluck('booking_id')->unique()->values()->all()];

            if ($salary <= 0) {
                $row['note'] = 'No salary entered for this year so far';
            } else {
                $times = $collection / $salary;
                $slab = ($slabs['FOS'] ?? collect())
                    ->first(fn ($s) => $times >= (float) $s->from_times && ($s->to_times === null || $times < (float) $s->to_times));
                $multiplier = (float) ($slab->justification_multiplier ?? 4);
                $justification = $salary * $multiplier;
                $above = max(0, $collection - $justification);
                $percent = (float) ($slab->incentive_percent ?? 0);

                $row['performance_times'] = round($times, 2);
                $row['justification_multiplier'] = $multiplier;
                $row['justification_amount'] = round($justification, 2);
                $row['incentive_base'] = round($above, 2);
                $row['slab_percent'] = $percent;
                $row['ytd_incentive'] = round($above * $percent / 100, 2);
            }
            $fosIncentive[$fosId] = $row['ytd_incentive'];
            $rows->push($row);
        }

        // ---------- Managers ----------
        foreach (self::MANAGER_COLUMNS as $role => $column) {
            $roleSlabs = ($slabs[$role] ?? collect())->sortByDesc('justification_multiplier');
            if ($roleSlabs->isEmpty()) {
                $warnings[] = "No {$role} slabs are set for {$fy}.";
            }

            foreach ($collections->whereNotNull($column)->groupBy($column) as $managerId => $items) {
                // Team members: the manager, the FOS on the team's bookings and any managers in between.
                $members = collect([(int) $managerId])->merge($items->pluck('fos_id'));
                if ($role === 'Sr. TL' || $role === 'CH') {
                    $members = $members->merge($items->pluck('tl_user_id'));
                }
                if ($role === 'CH') {
                    $members = $members->merge($items->pluck('sr_tl_user_id'));
                }
                $members = $members->filter()->unique();
                $teamSalary = round($members->sum(fn ($id) => $salaries[$id] ?? 0), 2);
                $teamCollection = round($items->sum('amount'), 2);

                // Team FOS incentive: each FOS's incentive, split by the share of their collection on this team's bookings.
                $teamFosIncentive = 0.0;
                foreach ($items->groupBy('fos_id') as $fosId => $fosItems) {
                    $fosTotal = $collections->where('fos_id', $fosId)->sum('amount');
                    if ($fosTotal > 0) {
                        $teamFosIncentive += ($fosIncentive[$fosId] ?? 0) * $fosItems->sum('amount') / $fosTotal;
                    }
                }

                $row = $this->baseRow($users[$managerId] ?? null, (int) $managerId, $role, $teamSalary, $teamCollection);
                $row['incentive_base'] = round($teamFosIncentive, 2);
                $row['details'] = [
                    'team_members' => $members->values()->all(),
                    'bookings' => $items->pluck('booking_id')->unique()->values()->all(),
                ];

                if ($teamSalary <= 0) {
                    $row['note'] = 'No salary entered for the team this year so far';
                } else {
                    $times = $teamCollection / $teamSalary;
                    $slab = $roleSlabs->first(fn ($s) => $times >= (float) $s->justification_multiplier);
                    $row['performance_times'] = round($times, 2);
                    if ($slab) {
                        $row['justification_multiplier'] = (float) $slab->justification_multiplier;
                        $row['justification_amount'] = round($teamSalary * (float) $slab->justification_multiplier, 2);
                        $row['slab_percent'] = (float) $slab->incentive_percent;
                        $row['ytd_incentive'] = round($teamFosIncentive * (float) $slab->incentive_percent / 100, 2);
                    } else {
                        $row['note'] = 'Team has not reached the first slab yet';
                    }
                }
                $rows->push($row);
            }
        }

        // ---------- Leavers, earlier quarters, payable ----------
        $rows = $rows->map(function ($row) use ($frozenBefore) {
            $row['previously_frozen'] = $frozenBefore[$row['user_id'].'|'.$row['role']] ?? 0.0;
            if ($row['status'] === 'Exited') {
                $row['payable'] = 0.0;
                $row['note'] = trim(($row['note'] ? $row['note'].'. ' : '').'Has left, not paid');
            } else {
                $row['payable'] = round(max(0, $row['ytd_incentive'] - $row['previously_frozen']), 2);
            }

            return $row;
        });

        $order = array_flip(self::ROLES);
        $rows = $rows->sortBy([fn ($a, $b) => $order[$a['role']] <=> $order[$b['role']], fn ($a, $b) => $b['payable'] <=> $a['payable'], fn ($a, $b) => strcmp($a['name'], $b['name'])])->values();

        return ['rows' => $rows, 'warnings' => $warnings];
    }

    /** Saves and locks the quarter. Returns how many rows were frozen. */
    public function freeze(string $fy, int $quarter, int $byUserId): int
    {
        if ($this->isFrozen($fy, $quarter)) {
            throw new \RuntimeException(self::quarterLabel($quarter)." {$fy} is already frozen.");
        }
        $result = $this->calculate($fy, $quarter);

        return DB::transaction(function () use ($result, $fy, $quarter, $byUserId) {
            IncentivePayout::where('financial_year', $fy)->where('quarter', $quarter)->whereNull('frozen_at')->delete();
            $now = now();
            foreach ($result['rows'] as $row) {
                IncentivePayout::create([
                    'user_id' => $row['user_id'],
                    'financial_year' => $fy,
                    'quarter' => $quarter,
                    'role' => $row['role'],
                    'ytd_salary' => $row['ytd_salary'],
                    'ytd_collection' => $row['ytd_collection'],
                    'performance_times' => $row['performance_times'],
                    'justification_multiplier' => $row['justification_multiplier'],
                    'justification_amount' => $row['justification_amount'],
                    'incentive_base' => $row['incentive_base'],
                    'slab_percent' => $row['slab_percent'],
                    'ytd_incentive' => $row['ytd_incentive'],
                    'previously_frozen' => $row['previously_frozen'],
                    'payable' => $row['payable'],
                    'note' => $row['note'],
                    'details' => $row['details'],
                    'frozen_by' => $byUserId,
                    'frozen_at' => $now,
                ]);
            }

            return $result['rows']->count();
        });
    }

    /** Unlocks a quarter so it can be worked out again. Later quarters must be unlocked first. */
    public function unfreeze(string $fy, int $quarter): int
    {
        if (IncentivePayout::where('financial_year', $fy)->where('quarter', '>', $quarter)->whereNotNull('frozen_at')->exists()) {
            throw new \RuntimeException('A later quarter of this year is frozen. Unfreeze that one first.');
        }

        return IncentivePayout::where('financial_year', $fy)->where('quarter', $quarter)->delete();
    }

    private function baseRow($user, int $userId, string $role, float $salary, float $collection): array
    {
        return [
            'user_id' => $userId,
            'name' => $user->name ?? ('User #'.$userId),
            'employee_code' => $user->employee_code ?? '',
            'status' => $user->status ?? '',
            'role' => $role,
            'ytd_salary' => round($salary, 2),
            'ytd_collection' => round($collection, 2),
            'performance_times' => 0.0,
            'justification_multiplier' => 0.0,
            'justification_amount' => 0.0,
            'incentive_base' => 0.0,
            'slab_percent' => 0.0,
            'ytd_incentive' => 0.0,
            'previously_frozen' => 0.0,
            'payable' => 0.0,
            'note' => null,
            'details' => [],
        ];
    }

    /** @return array<int, float> user_id => salary cost from April to the end of the quarter */
    private function salariesSoFar(string $fy, int $quarter): array
    {
        $months = array_slice(self::FY_MONTHS, 0, $quarter * 3);

        return DB::table('user_salaries')
            ->where('financial_year', $fy)
            ->whereIn(DB::raw('CAST(month AS UNSIGNED)'), $months)
            ->selectRaw('user_id, SUM(total_employee_cost) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id')
            ->map(fn ($v) => (float) $v)
            ->all();
    }

    /** One row per booking: brokerage received (bank + TDS) between the dates, with the booking's team. */
    private function collectionsSoFar(string $start, string $end): Collection
    {
        return DB::table('booking_brokerage_payments as p')
            ->join('bookings as b', 'b.id', '=', 'p.booking_id')
            ->whereNull('p.deleted_at')
            ->whereNull('b.deleted_at')
            ->whereNotNull('b.sales_user_id')
            ->whereRaw('(COALESCE(p.bank_received_amount, 0) + COALESCE(p.tds_amount, 0)) > 0')
            ->whereRaw('COALESCE(p.bank_received_date, p.invoice_date) BETWEEN ? AND ?', [$start, $end])
            ->groupBy('b.id', 'b.sales_user_id', 'b.tl_user_id', 'b.sr_tl_user_id', 'b.ch_user_id')
            ->selectRaw('b.id as booking_id, b.sales_user_id as fos_id, b.tl_user_id, b.sr_tl_user_id, b.ch_user_id,
                SUM(COALESCE(p.bank_received_amount, 0) + COALESCE(p.tds_amount, 0)) as amount')
            ->get()
            ->map(function ($r) {
                $r->amount = (float) $r->amount;

                return $r;
            });
    }
}

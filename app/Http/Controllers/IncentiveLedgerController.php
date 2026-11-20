<?php

namespace App\Http\Controllers;

use App\Models\IncentivePayout;
use App\Models\IncentiveSlab;
use App\Services\QuarterlyIncentiveService;
use Illuminate\Http\Request;

/**
 * Incentives > Ledger & Freeze: work out a quarter on the year so far, freeze it,
 * and see what was frozen before.
 */
class IncentiveLedgerController extends Controller
{
    public function __construct(private QuarterlyIncentiveService $service)
    {
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->can('incentive-ledger-view'), 403);

        $years = $this->financialYears();
        $fy = $request->input('fy', $years[0] ?? '2025-26');
        $quarter = (int) $request->input('quarter', $this->currentQuarter($fy));
        $quarter = max(1, min(4, $quarter));

        $frozen = $this->service->isFrozen($fy, $quarter);
        if ($frozen) {
            $rows = IncentivePayout::with('user:id,name,employee_code,status')
                ->where('financial_year', $fy)->where('quarter', $quarter)
                ->get()
                ->map(fn ($p) => $p->toArray() + [
                    'name' => $p->user->name ?? '',
                    'employee_code' => $p->user->employee_code ?? '',
                    'status' => $p->user->status ?? '',
                ]);
            $order = array_flip(QuarterlyIncentiveService::ROLES);
            $rows = $rows->sortBy([fn ($a, $b) => ($order[$a['role']] ?? 9) <=> ($order[$b['role']] ?? 9), fn ($a, $b) => $b['payable'] <=> $a['payable']])->values();
            $frozenInfo = IncentivePayout::with('frozenBy:id,name')->where('financial_year', $fy)->where('quarter', $quarter)->first();
            $warnings = [];
        } else {
            $result = $this->service->calculate($fy, $quarter);
            $rows = $result['rows'];
            $warnings = $result['warnings'];
            $frozenInfo = null;
        }

        $role = $request->input('role');
        if ($role) {
            $rows = $rows->where('role', $role)->values();
        }

        $quarterStatus = [];
        foreach ([1, 2, 3, 4] as $q) {
            $quarterStatus[$q] = $this->service->isFrozen($fy, $q);
        }

        return view('incentives.ledger', [
            'years' => $years,
            'fy' => $fy,
            'quarter' => $quarter,
            'role' => $role,
            'rows' => $rows,
            'warnings' => $warnings,
            'frozen' => $frozen,
            'frozenInfo' => $frozenInfo,
            'quarterStatus' => $quarterStatus,
            'period' => QuarterlyIncentiveService::period($fy, $quarter),
            'canFreeze' => $request->user()->can('incentive-calculation-create'),
            'canUnfreeze' => $request->user()->hasRole('Superadmin'),
        ]);
    }

    public function freeze(Request $request)
    {
        abort_unless($request->user()->can('incentive-calculation-create'), 403);
        $data = $request->validate([
            'fy' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'quarter' => ['required', 'integer', 'between:1,4'],
        ]);

        try {
            $count = $this->service->freeze($data['fy'], (int) $data['quarter'], $request->user()->id);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('incentive-ledger.index', ['fy' => $data['fy'], 'quarter' => $data['quarter']])
            ->with('success', QuarterlyIncentiveService::quarterLabel((int) $data['quarter'])." {$data['fy']} frozen for {$count} people.");
    }

    public function unfreeze(Request $request)
    {
        abort_unless($request->user()->hasRole('Superadmin'), 403);
        $data = $request->validate([
            'fy' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'quarter' => ['required', 'integer', 'between:1,4'],
        ]);

        try {
            $this->service->unfreeze($data['fy'], (int) $data['quarter']);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('incentive-ledger.index', ['fy' => $data['fy'], 'quarter' => $data['quarter']])
            ->with('success', QuarterlyIncentiveService::quarterLabel((int) $data['quarter'])." {$data['fy']} unfrozen.");
    }

    public function export(Request $request)
    {
        abort_unless($request->user()->can('incentive-ledger-view'), 403);
        $fy = $request->input('fy', '2025-26');
        $quarter = max(1, min(4, (int) $request->input('quarter', 1)));

        $rows = $this->service->isFrozen($fy, $quarter)
            ? IncentivePayout::with('user:id,name,employee_code,status')->where('financial_year', $fy)->where('quarter', $quarter)->get()
                ->map(fn ($p) => $p->toArray() + ['name' => $p->user->name ?? '', 'employee_code' => $p->user->employee_code ?? '', 'status' => $p->user->status ?? ''])
            : $this->service->calculate($fy, $quarter)['rows'];

        $filename = "incentives-{$fy}-Q{$quarter}.csv";

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Employee code', 'Name', 'Status', 'Role', 'Salary so far', 'Collection so far', 'Times',
                'Justification', 'Incentive base', 'Slab %', 'Incentive so far', 'Frozen earlier', 'Payable this quarter', 'Note']);
            foreach ($rows as $r) {
                fputcsv($out, [$r['employee_code'], $r['name'], $r['status'], $r['role'], $r['ytd_salary'], $r['ytd_collection'],
                    $r['performance_times'], $r['justification_amount'], $r['incentive_base'], $r['slab_percent'],
                    $r['ytd_incentive'], $r['previously_frozen'], $r['payable'], $r['note']]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /** Financial years that have slabs, salaries or bookings, newest first. */
    protected function financialYears(): array
    {
        $years = IncentiveSlab::distinct()->pluck('financial_year')
            ->merge(\DB::table('user_salaries')->distinct()->pluck('financial_year'))
            ->filter(fn ($y) => preg_match('/^\d{4}-\d{2}$/', (string) $y))
            ->unique()->sortDesc()->values()->all();

        return $years ?: ['2025-26'];
    }

    protected function currentQuarter(string $fy): int
    {
        $startYear = (int) substr($fy, 0, 4);
        $today = now();
        if ($today->lt(\Carbon\Carbon::create($startYear, 4, 1))) {
            return 1;
        }
        if ($today->gt(\Carbon\Carbon::create($startYear + 1, 3, 31))) {
            return 4;
        }
        $month = (int) $today->format('n');

        return $month >= 4 && $month <= 6 ? 1 : ($month >= 7 && $month <= 9 ? 2 : ($month >= 10 ? 3 : 4));
    }
}

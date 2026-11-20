<?php

namespace App\Http\Controllers;

use App\Models\IncentivePayout;
use App\Services\QuarterlyIncentiveService;
use Illuminate\Http\Request;

/** KeyEarn > My incentive: a person's own quarterly incentive, worked out on the year so far. */
class MyIncentiveController extends IncentiveLedgerController
{
    public function show(Request $request)
    {
        $user = $request->user();
        $years = $this->financialYears();
        $fy = $request->input('fy', $years[0] ?? '2025-26');
        $lastQuarter = $this->currentQuarter($fy);

        $service = app(QuarterlyIncentiveService::class);
        $quarters = [];
        foreach (range(1, $lastQuarter) as $q) {
            if ($service->isFrozen($fy, $q)) {
                $rows = IncentivePayout::where('financial_year', $fy)->where('quarter', $q)->where('user_id', $user->id)->get()
                    ->map(fn ($p) => $p->toArray());
            } else {
                $rows = $service->calculate($fy, $q)['rows']->where('user_id', $user->id)->values();
            }
            $quarters[$q] = ['frozen' => $service->isFrozen($fy, $q), 'rows' => $rows, 'period' => QuarterlyIncentiveService::period($fy, $q)];
        }

        return view('incentives.mine', compact('years', 'fy', 'quarters'));
    }
}

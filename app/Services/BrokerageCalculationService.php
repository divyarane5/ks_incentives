<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Project;
use App\Models\ProjectLadder;
use App\Models\DeveloperLadder;

class BrokerageCalculationService
{

    public function recalculateAll($projectId)
    {
        $project = Project::findOrFail($projectId);

        $basePercent = (float) $project->base_brokerage_percent;

        $bookings = Booking::where('project_id', $projectId)
            ->where('booking_confirm', 'approved')
            ->whereNotNull('booking_date')
            ->orderBy('booking_date')
            ->orderBy('id')
            ->get();

        if ($bookings->isEmpty()) {
            return;
        }

        /* =========================================================
        * STEP 1: CALCULATE FINAL LADDER PER DATE WINDOW (FLAT)
        * ========================================================= */
        $finalLadderMap = [];

        $projectLadders = ProjectLadder::where('project_id', $projectId)
            ->where('status', 1)
            ->get()
            ->groupBy(function ($ladder) {
                return date('Y-m-d', strtotime($ladder->project_s_date)) . '_' .
                    date('Y-m-d', strtotime($ladder->project_e_date));
            });

        foreach ($projectLadders as $key => $ladders) {

            $first = $ladders->first();

            $startDate = date('Y-m-d', strtotime($first->project_s_date));
            $endDate   = date('Y-m-d', strtotime($first->project_e_date));

            // ✅ TOTAL deals in this window
            $totalDeals = Booking::where('project_id', $projectId)
                ->where('booking_confirm', 'approved')
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->count();

            // ✅ FINAL ladder based on TOTAL deals (not per booking)
            $finalPercent = ProjectLadder::where('project_id', $projectId)
                ->where('status', 1)
                ->whereDate('project_s_date', $startDate)
                ->whereDate('project_e_date', $endDate)
                ->where('s_booking', '<=', $totalDeals)
                ->where(function ($q) use ($totalDeals) {
                    $q->where('e_booking', '>=', $totalDeals)
                    ->orWhereNull('e_booking');
                })
                ->orderBy('s_booking', 'desc')
                ->value('ladder') ?? 0;

            $finalLadderMap[$key] = (float) $finalPercent;
        }

        /* =========================================================
        * STEP 2: APPLY SAME LADDER TO ALL BOOKINGS IN WINDOW
        * ========================================================= */
        foreach ($bookings as $booking) {

            $sitePercent = 0;

            $ladder = ProjectLadder::where('project_id', $projectId)
                ->where('status', 1)
                ->whereDate('project_s_date', '<=', $booking->booking_date)
                ->whereDate('project_e_date', '>=', $booking->booking_date)
                ->orderBy('project_s_date', 'desc')
                ->first();

            if ($ladder) {

                $key = date('Y-m-d', strtotime($ladder->project_s_date)) . '_' .
                    date('Y-m-d', strtotime($ladder->project_e_date));

                $sitePercent = $finalLadderMap[$key] ?? 0;
            }

            /* =========================================================
            * STEP 3: AOP CALCULATION
            * ========================================================= */
            $aopPercent = 0;

            $aopLadder = DeveloperLadder::where('developer_id', $booking->developer_id)
                ->where('status', 1)
                ->whereDate('aop_s_date', '<=', $booking->booking_date)
                ->whereDate('aop_e_date', '>=', $booking->booking_date)
                ->orderBy('aop_s_date', 'desc')
                ->first();

            if ($aopLadder) {

                $developerRevenue = Booking::where('developer_id', $booking->developer_id)
                    ->where('booking_confirm', 'approved')
                    ->whereBetween('booking_date', [
                        $aopLadder->aop_s_date,
                        $aopLadder->aop_e_date
                    ])
                    ->whereDate('booking_date', '<=', $booking->booking_date)
                    ->sum('agreement_value');

                $aopPercent = DeveloperLadder::where('developer_id', $booking->developer_id)
                    ->where('status', 1)
                    ->whereDate('aop_s_date', '<=', $booking->booking_date)
                    ->whereDate('aop_e_date', '>=', $booking->booking_date)
                    ->where('min_aop', '<=', $developerRevenue)
                    ->where(function ($q) use ($developerRevenue) {
                        $q->where('max_aop', '>=', $developerRevenue)
                        ->orWhereNull('max_aop');
                    })
                    ->orderBy('min_aop', 'desc')
                    ->value('ladder') ?? 0;
            }

            /* =========================================================
            * STEP 4: FINAL CALCULATION
            * ========================================================= */
            $totalPercent = $basePercent + $sitePercent + $aopPercent;

            $agreementValue = (float) $booking->agreement_value;

            $brokerageAmount = round(($agreementValue * $totalPercent) / 100, 2);

            $finalRevenue = $brokerageAmount
                + ($booking->additional_kicker ?? 0)
                - ($booking->passback ?? 0);

            /* =========================================================
            * STEP 5: SAVE
            * ========================================================= */
            $booking->base_brokerage_percent = $basePercent;
            $booking->site_ladder_percent    = $sitePercent;
            $booking->aop_ladder_percent     = $aopPercent;
            $booking->total_brokerage_percent = $totalPercent;
            $booking->current_effective_amount = $brokerageAmount;
            $booking->final_revenue          = $finalRevenue;
            $booking->amount_receivable      = round($finalRevenue * 0.98, 2);
            $booking->tds_amount             = round($finalRevenue * 0.02, 2);

            $booking->save();
        }
    }

    // private function getProjectLadderPercent($projectId, $dealCount, $bookingDate)
    // {
    //     return ProjectLadder::where('project_id', $projectId)
    //         ->where('status', 1)

    //         // ✅ TIME WINDOW FILTER (MOST IMPORTANT FIX)
    //         ->whereDate('project_s_date', '<=', $bookingDate)
    //         ->whereDate('project_e_date', '>=', $bookingDate)

    //         // optional fallback safety
    //         ->where('s_booking', '<=', $dealCount)
    //         ->where(function ($q) use ($dealCount) {
    //             $q->where('e_booking', '>=', $dealCount)
    //             ->orWhereNull('e_booking');
    //         })

    //         ->orderBy('s_booking', 'desc')
    //         ->first()
    //         ?->ladder ?? 0;
    // }

    private function getAopPercent($developerId, $revenue, $bookingDate)
    {
        $ladder = DeveloperLadder::where('developer_id', $developerId)
            ->where('status', 1)
            ->whereDate('aop_s_date', '<=', $bookingDate)
            ->whereDate('aop_e_date', '>=', $bookingDate)
            ->where('min_aop', '<=', $revenue)
            ->where(function ($q) use ($revenue) {
                $q->where('max_aop', '>=', $revenue)
                  ->orWhereNull('max_aop');
            })
            ->orderBy('min_aop', 'desc')
            ->first();

        return $ladder ? $ladder->ladder : 0;
    }
}
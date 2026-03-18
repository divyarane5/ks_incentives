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

        $basePercent = $project->base_brokerage_percent;

        $bookings = Booking::where('project_id', $projectId)
            ->orderBy('booking_date')
            ->orderBy('id')
            ->get();

        if ($bookings->isEmpty()) {
            return;
        }

        // TOTAL BOOKINGS OF PROJECT
        $finalDealCount = Booking::where('project_id', $projectId)->count();

        // GET FINAL SITE LADDER %
        $finalSitePercent = $this->getProjectLadderPercent(
            $projectId,
            $finalDealCount,
            $bookings->last()->booking_date
        );
       // dd($finalDealCount, $finalSitePercent);
        $developerTotals = Booking::where('project_id', $projectId)
            ->selectRaw('developer_id, SUM(agreement_value) as total_revenue')
            ->groupBy('developer_id')
            ->pluck('total_revenue', 'developer_id');

        foreach ($bookings as $booking) {

            $developerTotalRevenue = $developerTotals[$booking->developer_id] ?? 0;

            $aopPercent = $this->getAopPercent(
                $booking->developer_id,
                $developerTotalRevenue,
                $booking->booking_date
            );

            $siteIncrement = $finalSitePercent - $basePercent;
            $totalPercent = $basePercent + $siteIncrement + $aopPercent;

            $brokerageAmount = ($booking->agreement_value * $totalPercent) / 100;

            $finalRevenue = $brokerageAmount + $booking->additional_kicker - $booking->passback;

            $booking->base_brokerage_percent = $basePercent;
            $booking->site_ladder_percent = $finalSitePercent;
            $booking->aop_ladder_percent = $aopPercent;
            $booking->total_brokerage_percent = $totalPercent;
            $booking->current_effective_amount = $brokerageAmount;
            $booking->final_revenue = $finalRevenue;
            $booking->amount_receivable = round($finalRevenue * 0.98, 2);
            $booking->tds_amount = round($finalRevenue * 0.02, 2);

            $booking->save();
        }
    }

    private function getProjectLadderPercent($projectId, $dealCount, $bookingDate)
    {
        $ladder = ProjectLadder::where('project_id', $projectId)
            ->where('status', 1)
            ->whereDate('project_s_date', '<=', $bookingDate)
            ->whereDate('project_e_date', '>=', $bookingDate)
            ->where('s_booking', '<=', $dealCount)
            ->where(function ($q) use ($dealCount) {
                $q->where('e_booking', '>=', $dealCount)
                  ->orWhereNull('e_booking');
            })
            ->orderBy('s_booking', 'desc')
            ->first();

        return $ladder ? $ladder->ladder : 0;
    }

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
            ->first();

        return $ladder ? $ladder->ladder : 0;
    }
}
<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Project;
use App\Models\ProjectLadder;
use App\Models\DeveloperLadder;
use Carbon\Carbon;

class BrokerageCalculationService
{
    public function calculate($projectId, $developerId, $agreementValue, $bookingDate)
    {
        //echo $projectId; echo $developerId; echo $agreementValue; echo $bookingDate; exit;
        $date = Carbon::parse($bookingDate);

        // 1️⃣ Get Base Brokerage From Project
        $project = Project::findOrFail($projectId);
        $basePercent = $project->base_brokerage_percent;

        // 2️⃣ Count Project Bookings In Ladder Date Range
        $projectDealCount = Booking::where('project_id', $projectId)
            ->whereDate('booking_date', '<=', $bookingDate)
            ->count() + 1;

        // 3️⃣ Get Active Project Ladder
        $projectLadder = ProjectLadder::where('project_id', $projectId)
            ->where('status', 1)
            ->whereDate('project_s_date', '<=', $bookingDate)
            ->whereDate('project_e_date', '>=', $bookingDate)
            ->where('s_booking', '<=', $projectDealCount)
            ->where(function ($q) use ($projectDealCount) {
                $q->where('e_booking', '>=', $projectDealCount)
                  ->orWhereNull('e_booking');
            })
            ->first();

        $sitePercent = $projectLadder ? $projectLadder->ladder : $basePercent;
        $siteIncrement = $sitePercent - $basePercent;

        // 4️⃣ Calculate Developer Revenue In AOP Date Range
        $developerRevenue = Booking::where('developer_id', $developerId)
            ->whereDate('booking_date', '<=', $bookingDate)
            ->sum('agreement_value') + $agreementValue;

        // 5️⃣ Get Active Developer AOP Ladder
        $aopLadder = DeveloperLadder::where('developer_id', $developerId)
            ->where('status', 1)
            ->whereDate('aop_s_date', '<=', $bookingDate)
            ->whereDate('aop_e_date', '>=', $bookingDate)
            ->where('min_aop', '<=', $developerRevenue)
            ->where(function ($q) use ($developerRevenue) {
                $q->where('max_aop', '>=', $developerRevenue)
                  ->orWhereNull('max_aop');
            })
            ->first();

        $aopPercent = $aopLadder ? $aopLadder->ladder : 0;

        // 6️⃣ Final Brokerage %
        $totalPercent = $sitePercent + $aopPercent;

        $brokerageAmount = ($agreementValue * $totalPercent) / 100;

        return [
            'base_percent' => $basePercent,
            'project_deal_count' => $projectDealCount,
            'site_percent' => $sitePercent,
            'site_increment' => $siteIncrement,
            'developer_revenue' => $developerRevenue,
            'aop_percent' => $aopPercent,
            'total_percent' => $totalPercent,
            'brokerage_amount' => $brokerageAmount
        ];
    }
}
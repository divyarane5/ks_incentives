<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Collection;

class BookingRevenueService
{
    /**
     * Calculate full revenue breakdown for a booking
     */
    public function calculate(Booking $booking): array
    {
        $agreementValue = (float) $booking->agreement_value;

        $projectPercent   = $this->getProjectLadderPercent($booking);
        $developerPercent = $this->getDeveloperLadderPercent($booking);

        $totalPercent = $projectPercent + $developerPercent;

        $baseRevenue = ($agreementValue * $totalPercent) / 100;

        return [
            'agreement_value'   => $agreementValue,
            'project_percent'   => $projectPercent,
            'developer_percent' => $developerPercent,
            'total_percent'     => $totalPercent,
            'base_revenue'      => round($baseRevenue, 2),
        ];
    }

    /**
     * Get applicable project ladder percentage
     */
    private function getProjectLadderPercent(Booking $booking): float
    {
        if (!$booking->relationLoaded('project') || !$booking->project) {
            return 0;
        }

        $dealCount = (int) $booking->deal_count ?? 0;

        $ladders = $booking->project->projectLadders ?? collect();

        return $this->matchLadderByDealCount($ladders, $dealCount);
    }

    /**
     * Get applicable developer ladder percentage
     */
    private function getDeveloperLadderPercent(Booking $booking): float
    {
        if (!$booking->relationLoaded('developer') || !$booking->developer) {
            return 0;
        }

        $aopValue = (float) $booking->aop ?? 0;

        $ladders = $booking->developer->developerLadders ?? collect();

        return $this->matchLadderByAop($ladders, $aopValue);
    }

    /**
     * Match ladder based on deal count
     */
    private function matchLadderByDealCount(Collection $ladders, int $dealCount): float
    {
        foreach ($ladders as $ladder) {

            if (
                $dealCount >= $ladder->min_deals &&
                $dealCount <= $ladder->max_deals
            ) {
                return (float) $ladder->percentage;
            }
        }

        return 0;
    }

    /**
     * Match ladder based on AOP value
     */
    private function matchLadderByAop(Collection $ladders, float $aop): float
    {
        foreach ($ladders as $ladder) {

            if (
                $aop >= $ladder->min_aop &&
                $aop <= $ladder->max_aop
            ) {
                return (float) $ladder->percentage;
            }
        }

        return 0;
    }
}
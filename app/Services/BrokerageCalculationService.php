<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Project;
use App\Models\ProjectLadder;
use App\Models\DeveloperLadder;

class BrokerageCalculationService
{
    /**
     * Re-run brokerage after a booking is saved or deleted (called from the Booking model, so it
     * also covers changes made outside BookingController). Site ladder is per project; the AOP
     * ladder is per developer, so the old and new project and developer are all redone.
     */
    public function recalculateForBooking(Booking $booking): void
    {
        $projectIds = array_filter(array_unique([
            $booking->project_id,
            $booking->getOriginal('project_id'),
        ]));
        $developerIds = array_filter(array_unique([
            $booking->developer_id,
            $booking->getOriginal('developer_id'),
        ]));

        foreach ($projectIds as $projectId) {
            $this->recalculateAll($projectId);
            // recalculateAll leaves AOP at 0, so redo AOP for every developer on these projects.
            $developerIds = array_merge($developerIds, Booking::where('project_id', $projectId)
                ->whereNotNull('developer_id')->distinct()->pluck('developer_id')->all());
        }

        foreach (array_unique(array_map('intval', $developerIds)) as $developerId) {
            $this->recalculateDeveloperAop($developerId);
        }
    }

    public function recalculateAll($projectId)
    {
        $project = Project::find($projectId);

        if (! $project) {
            return;
        }

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

        foreach ($bookings as $booking) {

            /* =========================================================
            * STEP 1: SITE LADDER (FIXED)
            * ========================================================= */

            $sitePercent = 0;

            $window = ProjectLadder::where('project_id', $projectId)
                ->where('status', 1)
                ->whereDate('project_s_date', '<=', $booking->booking_date)
                ->whereDate('project_e_date', '>=', $booking->booking_date)
                ->orderByDesc('project_s_date')
                ->first();

            if ($window) {

                $dealCount = Booking::where('project_id', $projectId)
                    ->where('booking_confirm', 'approved')
                    ->whereBetween('booking_date', [
                        $window->project_s_date,
                        $window->project_e_date
                    ])
                    ->count();

                $sitePercent = ProjectLadder::where('project_id', $projectId)
                    ->where('status', 1)
                    ->where('project_s_date', $window->project_s_date)
                    ->where('project_e_date', $window->project_e_date)
                    ->where('s_booking', '<=', $dealCount)
                    ->where(function ($q) use ($dealCount) {
                        $q->where('e_booking', '>=', $dealCount)
                        ->orWhereNull('e_booking');
                    })
                    ->orderByDesc('s_booking')
                    ->value('ladder') ?? 0;
            }

            /* =========================================================
            * STEP 2: AOP LADDER (CORRECT)
            * ========================================================= */

            $aopPercent = 0;

            // $aopWindow = DeveloperLadder::where('developer_id', $booking->developer_id)
            //     ->where('status', 1)
            //     ->whereDate('aop_s_date', '<=', $booking->booking_date)
            //     ->whereDate('aop_e_date', '>=', $booking->booking_date)
            //     ->orderByDesc('aop_s_date')
            //     ->first();

            // if ($aopWindow) {

            //     $developerRevenue = Booking::where('developer_id', $booking->developer_id)
            //         ->where('booking_confirm', 'approved')
            //         ->whereBetween('booking_date', [
            //             $aopWindow->aop_s_date,
            //             $aopWindow->aop_e_date
            //         ])
            //         ->whereDate('booking_date', '<=', $booking->booking_date)
            //         ->sum('agreement_value');

            //     $aopPercent = DeveloperLadder::where('developer_id', $booking->developer_id)
            //         ->where('status', 1)
            //         ->whereDate('aop_s_date', '<=', $booking->booking_date)
            //         ->whereDate('aop_e_date', '>=', $booking->booking_date)
            //         ->where('min_aop', '<=', $developerRevenue)
            //         ->where(function ($q) use ($developerRevenue) {
            //             $q->where('max_aop', '>=', $developerRevenue)
            //             ->orWhereNull('max_aop');
            //         })
            //         ->orderByDesc('min_aop')
            //         ->value('ladder') ?? 0;
            // }

            /* =========================================================
            * STEP 3: FINAL CALCULATION
            * ========================================================= */

            $totalPercent = $basePercent + $sitePercent + $aopPercent;

            $agreementValue = (float) $booking->agreement_value;

            $brokerageAmount = round(($agreementValue * $totalPercent) / 100, 2);

            $finalRevenue = $brokerageAmount
                + ($booking->additional_kicker ?? 0)
                - ($booking->passback ?? 0);

            /* =========================================================
            * STEP 4: SAVE
            * ========================================================= */

            $booking->base_brokerage_percent   = $basePercent;
            $booking->site_ladder_percent      = $sitePercent;
            $booking->aop_ladder_percent       = $aopPercent;
            $booking->total_brokerage_percent  = $totalPercent;
            $booking->current_effective_amount = $brokerageAmount;
            $booking->final_revenue            = $finalRevenue;
            $booking->amount_receivable        = round($finalRevenue * 0.98, 2);
            $booking->tds_amount               = round($finalRevenue * 0.02, 2);

            $booking->save();
        }
    }
    public function recalculateDeveloperAop($developerId)
    {
        /*
        |--------------------------------------------------------------------------
        | Get all active AOP periods for this developer
        |--------------------------------------------------------------------------
        */
    
        $aopPeriods = DeveloperLadder::where('developer_id', $developerId)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->select('aop_s_date', 'aop_e_date')
            ->distinct()
            ->get();
    
        if ($aopPeriods->isEmpty()) {
            return;
        }
    
        /*
        |--------------------------------------------------------------------------
        | Process each AOP period separately
        |--------------------------------------------------------------------------
        */
    
        foreach ($aopPeriods as $period) {
    
            /*
            |--------------------------------------------------------------------------
            | Get ALL approved bookings of this developer
            | within this AOP period
            |--------------------------------------------------------------------------
            */
    
            $bookings = Booking::where('developer_id', $developerId)
                ->where('booking_confirm', 'approved')
                ->whereNotNull('booking_date')
                ->whereBetween('booking_date', [
                    $period->aop_s_date,
                    $period->aop_e_date
                ])
                ->orderBy('booking_date')
                ->orderBy('id')
                ->get();
    
            if ($bookings->isEmpty()) {
                continue;
            }
    
            /*
            |--------------------------------------------------------------------------
            | Calculate TOTAL agreement value for the entire AOP period
            |--------------------------------------------------------------------------
            */
    
            $totalAgreement = $bookings->sum('agreement_value');
    
            /*
            |--------------------------------------------------------------------------
            | Find the applicable AOP ladder
            |--------------------------------------------------------------------------
            */
    
            $ladder = DeveloperLadder::where('developer_id', $developerId)
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->where('aop_s_date', $period->aop_s_date)
                ->where('aop_e_date', $period->aop_e_date)
                ->where('min_aop', '<=', $totalAgreement)
                ->where(function ($q) use ($totalAgreement) {
                    $q->where('max_aop', '>=', $totalAgreement)
                        ->orWhereNull('max_aop');
                })
                ->orderByDesc('min_aop')
                ->first();
    
            /*
            |--------------------------------------------------------------------------
            | AOP percentage for ALL bookings in this period
            |--------------------------------------------------------------------------
            */
    
            $aopPercent = $ladder ? (float) $ladder->ladder : 0;
    
            /*
            |--------------------------------------------------------------------------
            | Update every booking in this AOP period
            |--------------------------------------------------------------------------
            */
    
            foreach ($bookings as $booking) {
    
                $basePercent = (float) $booking->base_brokerage_percent;
                $sitePercent = (float) $booking->site_ladder_percent;
    
                $totalPercent = $basePercent
                    + $sitePercent
                    + $aopPercent;
    
                $agreementValue = (float) $booking->agreement_value;
    
                $brokerageAmount = round(
                    ($agreementValue * $totalPercent) / 100,
                    2
                );
    
                $finalRevenue = $brokerageAmount
                    + ($booking->additional_kicker ?? 0)
                    - ($booking->passback ?? 0);
    
                $booking->aop_ladder_percent = $aopPercent;
                $booking->total_brokerage_percent = $totalPercent;
                $booking->current_effective_amount = $brokerageAmount;
                $booking->final_revenue = $finalRevenue;
                $booking->amount_receivable = round($finalRevenue * 0.98, 2);
                $booking->tds_amount = round($finalRevenue * 0.02, 2);
    
                $booking->save();
            }
        }
    }

}

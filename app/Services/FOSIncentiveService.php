<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSalary;
use App\Models\Booking;
use App\Models\IncentiveSlab;
use App\Models\IncentiveCalculation;

class FOSIncentiveService
{
    public function calculate($fy = '2025-26')
    {

        // Remove old calculations for FY
        IncentiveCalculation::where('financial_year', $fy)->delete();

        // Get all FOS users
        $users = User::where('role', 'FOS')
            ->where('business_unit', 'Keystone Real Estate Advisory')
            ->get();

        foreach($users as $user){

            // -------------------------
            // ANNUAL SALARY
            // -------------------------

            $annualSalary = UserSalary::where('user_id', $user->id)
                ->where('financial_year', $fy)
                ->sum('salary_credited');

            // -------------------------
            // BOOKING COLLECTION
            // -------------------------

            /*
            Replace this logic with your actual collection logic
            */

            $collection = Booking::where('employee_id', $user->id)
                ->where('financial_year', $fy)
                ->sum('booking_collection');

            // Avoid divide by zero
            if($annualSalary <= 0){

                $times = 0;

            }else{

                $times = $collection / $annualSalary;
            }

            // -------------------------
            // FIND SLAB
            // -------------------------

            $slab = IncentiveSlab::where('financial_year', $fy)
                ->where('role', 'FOS')
                ->where('from_times', '<=', $times)
                ->where('to_times', '>', $times)
                ->first();

            if(!$slab){
                continue;
            }

            // -------------------------
            // JUSTIFICATION
            // -------------------------

            $justification = $annualSalary * $slab->justification_multiplier;

            // -------------------------
            // ELIGIBLE AMOUNT
            // -------------------------

            $eligibleAmount = $collection - $justification;

            if($eligibleAmount < 0){
                $eligibleAmount = 0;
            }

            // -------------------------
            // INCENTIVE
            // -------------------------

            $incentive = ($eligibleAmount * $slab->incentive_percent) / 100;

            // -------------------------
            // SAVE
            // -------------------------

            IncentiveCalculation::create([

                'user_id' => $user->id,

                'financial_year' => $fy,

                'annual_salary' => $annualSalary,

                'collection' => $collection,

                'times' => round($times, 2),

                'slab_percent' => $slab->incentive_percent,

                'justification' => $justification,

                'eligible_amount' => $eligibleAmount,

                'incentive' => $incentive,
            ]);
        }
    }
}
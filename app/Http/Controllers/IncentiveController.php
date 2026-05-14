<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSalary;
use App\Models\Booking;
use App\Models\BookingBrokeragePayment;
use App\Models\IncentiveSlab;
use Illuminate\Http\Request;
use App\Models\IncentiveCalculation;
use Illuminate\Support\Facades\DB;

class IncentiveController extends Controller
{
    public function preview()
    {
        return view('incentives.preview');
    }

    public function previewData(Request $request)
    {
        $fy = $request->financial_year;

        $role = $request->role;

        /*
        |--------------------------------------------------------------------------
        | FY DATE RANGE
        |--------------------------------------------------------------------------
        */

        $fyStart = '2025-04-01';

        $fyEnd = '2026-03-31';

        $calculations = [];

        /*
        |--------------------------------------------------------------------------
        | GET USERS
        |--------------------------------------------------------------------------
        */

        $users = User::whereHas('roles', function ($q) use ($role) {

                        $q->where('name', $role);

                    })
                    ->where('department_id', 1) // Sales
                    ->where('business_unit_id', 1) // KREA
                    ->where('status', 'Active')
                    ->get();

        foreach ($users as $user) {

            /*
            |--------------------------------------------------------------------------
            | ANNUAL SALARY
            |--------------------------------------------------------------------------
            */

            $annualSalary = UserSalary::where('user_id', $user->id)
                                ->where('financial_year', $fy)
                                ->sum('total_employee_cost');

            /*
            |--------------------------------------------------------------------------
            | COLLECTION RECEIVED
            |--------------------------------------------------------------------------
            */

            $collection = BookingBrokeragePayment::join(
                                'bookings',
                                'bookings.id',
                                '=',
                                'booking_brokerage_payments.booking_id'
                            )
                            ->where('bookings.sales_user_id', $user->id)

                            ->where('booking_brokerage_payments.status', 'received')

                            // ->whereBetween(
                            //     'booking_brokerage_payments.bank_received_date',
                            //     [$fyStart, $fyEnd]
                            // )

                            ->sum('booking_brokerage_payments.bank_received_amount');
            /*
            |--------------------------------------------------------------------------
            | SKIP INVALID USERS
            |--------------------------------------------------------------------------
            */

            if($annualSalary <= 0 || $collection <= 0){

                continue;
            }
            /*
            |--------------------------------------------------------------------------
            | PERFORMANCE TIMES
            |--------------------------------------------------------------------------
            */

            $times = 0;

            if ($annualSalary > 0) {

                $times = $collection / $annualSalary;
            }

            /*
            |--------------------------------------------------------------------------
            | FIND INCENTIVE SLAB
            |--------------------------------------------------------------------------
            */

            $slab = IncentiveSlab::where('financial_year', $fy)

                        ->where('role', $role)

                        ->where('from_times', '<', $times)

                        ->where('to_times', '>=', $times)

                        ->first();

            $slabPercent = $slab->incentive_percent ?? 0;

            $multiplier = $slab->justification_multiplier ?? 4;

            /*
            |--------------------------------------------------------------------------
            | JUSTIFICATION
            |--------------------------------------------------------------------------
            */

            $justification = $annualSalary * $multiplier;

            /*
            |--------------------------------------------------------------------------
            | ELIGIBLE COLLECTION
            |--------------------------------------------------------------------------
            */

            $eligibleAmount = $collection - $justification;

            if ($eligibleAmount < 0) {

                $eligibleAmount = 0;
            }

            /*
            |--------------------------------------------------------------------------
            | FINAL INCENTIVE
            |--------------------------------------------------------------------------
            */

            $incentive = ($eligibleAmount * $slabPercent) / 100;

            /*
            |--------------------------------------------------------------------------
            | PREVIEW DATA
            |--------------------------------------------------------------------------
            */

            $calculations[] = [

                'user_id' => $user->id,

                'name' => $user->name,

                'annual_salary' => $annualSalary,

                'collection' => $collection,

                'times' => round($times, 2),

                'slab_percent' => $slabPercent,

                'justification' => $justification,

                'eligible_amount' => $eligibleAmount,

                'incentive' => $incentive,
            ];
        }

        return view('incentives.preview', compact(
            'calculations',
            'fy',
            'role'
        ));
    }

    public function save(Request $request)
    {
        $fy = $request->financial_year;

        $role = $request->role;

        $calculations = $request->calculations ?? [];

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if(count($calculations) == 0){

            return redirect()->back()
                ->with('error', 'No calculations found.');
        }

        DB::beginTransaction();

        try{

            /*
            |--------------------------------------------------------------------------
            | DELETE OLD CALCULATIONS
            |--------------------------------------------------------------------------
            */

            IncentiveCalculation::where('financial_year', $fy)
                ->where('role', $role)
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | SAVE NEW CALCULATIONS
            |--------------------------------------------------------------------------
            */

            foreach($calculations as $row){

                $annualSalary = (float) $row['annual_salary'];

                $collection = (float) $row['collection'];

                $incentive = (float) $row['incentive'];

                /*
                |--------------------------------------------------------------------------
                | SKIP NON ELIGIBLE USERS
                |--------------------------------------------------------------------------
                */

                if(
                    $annualSalary <= 0 ||
                    $collection <= 0 ||
                    $incentive <= 0
                ){
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | SAVE
                |--------------------------------------------------------------------------
                */

                IncentiveCalculation::create([

                    'user_id' => $row['user_id'],

                    'financial_year' => $fy,

                    'role' => $role,

                    'annual_salary' => $annualSalary,

                    'booking_collection' => $collection,

                    'performance_times' => $row['times'],

                    'justification_amount' => $row['justification'],

                    'eligible_collection' => $row['eligible_amount'],

                    'incentive_percent' => $row['slab_percent'],

                    'final_incentive' => $incentive,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('incentives.preview')
                ->with('success', 'Incentive calculations saved successfully.');

        }catch(\Exception $e){

            DB::rollBack();

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function show(User $user)
    {
        $fy = request('fy', '2025-26');

        /*
        |--------------------------------------------------------------------------
        | INCENTIVE SUMMARY
        |--------------------------------------------------------------------------
        */

        $summary = IncentiveCalculation::where('user_id', $user->id)
                        ->where('financial_year', $fy)
                        ->first();

        /*
        |--------------------------------------------------------------------------
        | FY DATE RANGE
        |--------------------------------------------------------------------------
        */

        $fyStart = '2025-04-01';

        $fyEnd = '2026-03-31';

        /*
        |--------------------------------------------------------------------------
        | BOOKING BREAKDOWN
        |--------------------------------------------------------------------------
        */

        $bookings = BookingBrokeragePayment::join(
                    'bookings',
                    'bookings.id',
                    '=',
                    'booking_brokerage_payments.booking_id'
                )
                ->where('bookings.sales_user_id', $user->id)

                ->select(

                    'booking_brokerage_payments.*',

                    'bookings.client_name',

                    'bookings.id as booking_ref_id'
                )

                ->orderBy(
                    'booking_brokerage_payments.bank_received_date',
                    'DESC'
                )

                ->get();
        $total = $bookings->sum('bank_received_amount');
        return view('incentives.show', compact(

            'user',

            'summary',

            'bookings',

            'fy',

            'total'
        ));
    }
}
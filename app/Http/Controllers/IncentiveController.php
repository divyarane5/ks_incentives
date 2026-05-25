<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSalary;
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
        | FOS PREVIEW
        |--------------------------------------------------------------------------
        */

        if($role == 'FOS'){

            return $this->previewFOS($fy, $role);
        }

        /*
        |--------------------------------------------------------------------------
        | TL / SR TL / CH PREVIEW
        |--------------------------------------------------------------------------
        */

        return $this->previewHierarchy($fy, $role);
    }

    /*
    |--------------------------------------------------------------------------
    | FOS PREVIEW
    |--------------------------------------------------------------------------
    */

    public function previewFOS($fy, $role)
    {
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
                    ->where('department_id', 1)
                    ->where('business_unit_id', 1)
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

                            ->where(
                                'booking_brokerage_payments.status',
                                'received'
                            )

                            // ->whereBetween(
                            //     'booking_brokerage_payments.bank_received_date',
                            //     [$fyStart, $fyEnd]
                            // )

                            ->sum(
                                'booking_brokerage_payments.bank_received_amount'
                            );

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

            $times = $collection / $annualSalary;

            /*
            |--------------------------------------------------------------------------
            | FIND INCENTIVE SLAB
            |--------------------------------------------------------------------------
            */

            $slab = IncentiveSlab::where('financial_year', $fy)

                        ->where('role', $role)

                        ->where('from_times', '<=', $times)

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

                'justification_multiplier' => $multiplier,

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

    /*
    |--------------------------------------------------------------------------
    | TL / SR TL / CH PREVIEW
    |--------------------------------------------------------------------------
    */

    private function previewHierarchy($fy, $role)
    {
        $calculations = [];

        $users = User::whereHas('roles', function ($q) use ($role) {

                        $q->where('name', $role);

                    })
                    ->where('department_id', 1)
                    ->where('business_unit_id', 1)
                    ->where('status', 'Active')
                    ->get();

        foreach ($users as $user) {

            $teamIds = [];

            /*
            |--------------------------------------------------------------------------
            | TL => DIRECT FOS
            |--------------------------------------------------------------------------
            */

            if($role == 'TL'){

                $teamIds = User::where('reporting_manager_id', $user->id)
                            ->whereHas('roles', function ($q) {

                                $q->where('name', 'FOS');

                            })
                            ->pluck('id')
                            ->toArray();
            }

            /*
            |--------------------------------------------------------------------------
            | SR TL => FOS UNDER TL
            |--------------------------------------------------------------------------
            */

            if($role == 'Sr. TL'){

                $tlIds = User::where('reporting_manager_id', $user->id)
                            ->whereHas('roles', function ($q) {

                                $q->where('name', 'TL');

                            })
                            ->pluck('id')
                            ->toArray();

                $teamIds = User::whereIn('reporting_manager_id', $tlIds)
                            ->whereHas('roles', function ($q) {

                                $q->where('name', 'FOS');

                            })
                            ->pluck('id')
                            ->toArray();
            }

            /*
            |--------------------------------------------------------------------------
            | CH => FOS UNDER SR TL + TL
            |--------------------------------------------------------------------------
            */

            if($role == 'CH'){

                $srTlIds = User::where('reporting_manager_id', $user->id)
                                ->whereHas('roles', function ($q) {

                                    $q->where('name', 'Sr. TL');

                                })
                                ->pluck('id')
                                ->toArray();

                $tlIds = User::whereIn('reporting_manager_id', $srTlIds)
                            ->whereHas('roles', function ($q) {

                                $q->where('name', 'TL');

                            })
                            ->pluck('id')
                            ->toArray();

                $teamIds = User::whereIn('reporting_manager_id', $tlIds)
                            ->whereHas('roles', function ($q) {

                                $q->where('name', 'FOS');

                            })
                            ->pluck('id')
                            ->toArray();
            }

            if(count($teamIds) == 0){

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | TEAM SALARY
            |--------------------------------------------------------------------------
            */

            $teamSalary = UserSalary::whereIn('user_id', $teamIds)
                            ->where('financial_year', $fy)
                            ->sum('total_employee_cost');

            /*
            |--------------------------------------------------------------------------
            | TEAM COLLECTION
            |--------------------------------------------------------------------------
            */

            $teamCollection = BookingBrokeragePayment::join(
                                'bookings',
                                'bookings.id',
                                '=',
                                'booking_brokerage_payments.booking_id'
                            )
                            ->whereIn('bookings.sales_user_id', $teamIds)
                            ->where('booking_brokerage_payments.status', 'received')
                            ->sum('booking_brokerage_payments.bank_received_amount');

            /*
            |--------------------------------------------------------------------------
            | TOTAL FOS INCENTIVES
            |--------------------------------------------------------------------------
            */

            $teamIncentives = IncentiveCalculation::whereIn('user_id', $teamIds)
                                ->where('financial_year', $fy)
                                ->where('role', 'FOS')
                                ->sum('final_incentive');

            if($teamSalary <= 0 || $teamCollection <= 0){

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | PERFORMANCE TIMES
            |--------------------------------------------------------------------------
            */

            $times = $teamCollection / $teamSalary;

            /*
            |--------------------------------------------------------------------------
            | FIND SLAB USING JUSTIFICATION MULTIPLIER
            |--------------------------------------------------------------------------
            */

            
            $slab = IncentiveSlab::where('financial_year', $fy)
                        ->where('role', $role)
                        ->where('justification_multiplier', '<=', $times)
                        ->orderBy('justification_multiplier', 'DESC')
                        ->first();
            /*
            |--------------------------------------------------------------------------
            | NO ELIGIBILITY
            |--------------------------------------------------------------------------
            */

            if(!$slab){

                $justification = 0;

                $percent = 0;

                $finalIncentive = 0;

            }else{

                $justification = $teamSalary * $slab->justification_multiplier;

                $percent = $slab->incentive_percent;

                $finalIncentive = ($teamIncentives * $percent) / 100;
            }

            /*
            |--------------------------------------------------------------------------
            | PREVIEW
            |--------------------------------------------------------------------------
            */

            $calculations[] = [

                'user_id' => $user->id,

                'name' => $user->name,

                'annual_salary' => $teamSalary,

                'collection' => $teamCollection,

                'times' => round($times, 2),

                'justification_multiplier' => $slab->justification_multiplier ?? 0,

                'slab_percent' => $percent,

                'justification' => $justification,

                'eligible_amount' => $teamIncentives,

                'incentive' => $finalIncentive,
            ];
        }

        return view('incentives.preview', compact(
            'calculations',
            'fy',
            'role'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE
    |--------------------------------------------------------------------------
    */

    public function save(Request $request)
    {
        $fy = $request->financial_year;

        $role = $request->role;

        $calculations = $request->calculations ?? [];

        if(count($calculations) == 0){

            return redirect()->back()
                ->with('error', 'No calculations found.');
        }

        DB::beginTransaction();

        try{

            /*
            |--------------------------------------------------------------------------
            | DELETE OLD
            |--------------------------------------------------------------------------
            */

            IncentiveCalculation::where('financial_year', $fy)
                ->where('role', $role)
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | SAVE NEW
            |--------------------------------------------------------------------------
            */

            foreach($calculations as $row){

                $annualSalary = (float) (
                    $row['annual_salary']
                    ?? $row['team_salary']
                    ?? 0
                );

                $collection = (float) (
                    $row['collection']
                    ?? $row['team_collection']
                    ?? 0
                );

                $incentive = (float) ($row['incentive'] ?? 0);

                if(
                    $annualSalary <= 0 ||
                    $collection <= 0 
                ){
                    continue;
                }

                IncentiveCalculation::create([

                    'user_id' => $row['user_id'],

                    'financial_year' => $fy,

                    'role' => $role,

                    'annual_salary' => $annualSalary,

                    'booking_collection' => $collection,

                    'performance_times' => $row['times'] ?? 0,

                    'justification_amount' => $row['justification'] ?? 0,

                    'eligible_collection' => $row['eligible_amount'] ?? 0,

                    'incentive_percent' => $row['slab_percent'] ?? 0,

                    'final_incentive' => $incentive,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('incentives.preview')
                ->with(
                    'success',
                    'Incentive calculations saved successfully.'
                );

        }catch(\Exception $e){

            DB::rollBack();

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(User $user)
    {
        $fy = request('fy', '2025-26');

        /*
        |--------------------------------------------------------------------------
        | GET INCENTIVE SUMMARY
        |--------------------------------------------------------------------------
        */

        $summary = IncentiveCalculation::where('user_id', $user->id)
                        ->where('financial_year', $fy)
                        ->first();

        if(!$summary){

            abort(404, 'Incentive data not found.');
        }

        $role = $summary->role;

        $teamIds = [];

        /*
        |--------------------------------------------------------------------------
        | FOS
        |--------------------------------------------------------------------------
        */

        if($role == 'FOS'){

            $teamIds = [$user->id];
        }

        /*
        |--------------------------------------------------------------------------
        | TL => DIRECT FOS
        |--------------------------------------------------------------------------
        */

        if($role == 'TL'){

            $teamIds = User::where('reporting_manager_id', $user->id)
                        ->whereHas('roles', function ($q) {

                            $q->where('name', 'FOS');

                        })
                        ->pluck('id')
                        ->toArray();
        }

        /*
        |--------------------------------------------------------------------------
        | SR TL => FOS UNDER TL
        |--------------------------------------------------------------------------
        */

        if($role == 'Sr. TL'){

            $tlIds = User::where('reporting_manager_id', $user->id)
                        ->whereHas('roles', function ($q) {

                            $q->where('name', 'TL');

                        })
                        ->pluck('id')
                        ->toArray();

            $teamIds = User::whereIn('reporting_manager_id', $tlIds)
                        ->whereHas('roles', function ($q) {

                            $q->where('name', 'FOS');

                        })
                        ->pluck('id')
                        ->toArray();
        }

        /*
        |--------------------------------------------------------------------------
        | CH => FOS UNDER SR TL
        |--------------------------------------------------------------------------
        */

        if($role == 'CH'){

            $srTlIds = User::where('reporting_manager_id', $user->id)
                        ->whereHas('roles', function ($q) {

                            $q->where('name', 'Sr. TL');

                        })
                        ->pluck('id')
                        ->toArray();

            $tlIds = User::whereIn('reporting_manager_id', $srTlIds)
                        ->whereHas('roles', function ($q) {

                            $q->where('name', 'TL');

                        })
                        ->pluck('id')
                        ->toArray();

            $teamIds = User::whereIn('reporting_manager_id', $tlIds)
                        ->whereHas('roles', function ($q) {

                            $q->where('name', 'FOS');

                        })
                        ->pluck('id')
                        ->toArray();
        }

        /*
        |--------------------------------------------------------------------------
        | BOOKINGS
        |--------------------------------------------------------------------------
        */

        $bookings = BookingBrokeragePayment::join(
                        'bookings',
                        'bookings.id',
                        '=',
                        'booking_brokerage_payments.booking_id'
                    )
                    ->join(
                        'users',
                        'users.id',
                        '=',
                        'bookings.sales_user_id'
                    )
                    ->whereIn('bookings.sales_user_id', $teamIds)

                    ->select(

                        'booking_brokerage_payments.*',

                        'bookings.client_name',

                        'bookings.id as booking_ref_id',

                        'users.name as fos_name'
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
<?php

namespace App\Http\Controllers;

use App\Models\MandateProject;
use App\Models\ChannelPartner;
use App\Models\ClientEnquiry;
use App\Models\MandateBooking;
use App\Models\MandateBookingBrokerage;
use App\Models\Booking;
use App\Models\User;
use App\Models\Developer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role; // e.g., 'admin', 'manager', 'employee'
        $businessUnit = $user->business_unit_id ?? null;

        // Base queries
        $mandateBookingQuery   = MandateBooking::query();
        $mandateProjectQuery   = MandateProject::query();
        $channelPartnerQuery   = ChannelPartner::query();
        $clientEnquiryQuery    = ClientEnquiry::query();
        $mandateBookingBrokerageQuery = MandateBookingBrokerage::query();

        // Filter by business unit for non-admin users
        // if ($role !== 'admin' && $businessUnit) {
        //     $mandateBookingQuery->where('business_unit_id', $businessUnit);
        //     $mandateProjectQuery->where('business_unit_id', $businessUnit);
        //     $channelPartnerQuery->where('business_unit_id', $businessUnit);
        //     $clientEnquiryQuery->where('business_unit_id', $businessUnit);
        //     $mandateBookingBrokerageQuery->whereHas('booking', function($q) use ($businessUnit) {
        //         $q->where('business_unit_id', $businessUnit);
        //     });
        // }

        // Counts
        $totalRegistrations         = $mandateBookingQuery->count();
        $eligibleBrokerageBookings  = $mandateBookingBrokerageQuery->where('is_eligible', 1)
            ->distinct('booking_id')
            ->count('booking_id');

        $pendingBookings   = (clone $mandateBookingQuery)->where('booking_status', 'pending')->count();
        $completedBookings = (clone $mandateBookingQuery)->where('booking_status', 'completed')->count();
        $cancelledBookings = (clone $mandateBookingQuery)->where('booking_status', 'cancelled')->count();

        return view('dashboard', [
            'totalRegistrations'         => $totalRegistrations,
            'eligibleBrokerageBookings' => $eligibleBrokerageBookings,
            'pendingBookings'           => $pendingBookings,
            'completedBookings'         => $completedBookings,
            'cancelledBookings'         => $cancelledBookings,
            'mandateProjectsCount'      => $mandateProjectQuery->count(),
            'channelPartnersCount'      => $channelPartnerQuery->count(),
            'clientEnquiriesCount'      => $clientEnquiryQuery->count(),
            'mandateBookingsCount'      => $mandateBookingQuery->count(),
            'role'                      => $role,
            'businessUnit'              => $businessUnit,
        ]);
    }
    

    public function dashboardTwo(Request $request)
    {

    /* STORE FILTERS IN SESSION */

    if($request->isMethod('post')){

    session([
    'cluster_head'=>$request->cluster_head,
    'sr_tl'=>$request->sr_tl,
    'tl'=>$request->tl,
    'sales_manager'=>$request->sales_manager,
    'developer'=>$request->developer,
    'from_date'=>$request->from_date,
    'to_date'=>$request->to_date
    ]);

    }

    /* GET SESSION FILTERS */

    $cluster_head = session('cluster_head');
    $sr_tl = session('sr_tl');
    $tl = session('tl');
    $sales_manager = session('sales_manager');
    $developer = session('developer');
    $from_date = session('from_date');
    $to_date = session('to_date');


    /* BASE QUERY */

    $query = Booking::query();


    /* APPLY FILTERS */

    if($developer){
    $query->where('developer_id',$developer);
    }

    if($sales_manager){
    $query->where('sales_user_id',$sales_manager);
    }

    if($from_date){
    $query->whereDate('booking_date','>=',$from_date);
    }

    if($to_date){
    $query->whereDate('booking_date','<=',$to_date);
    }


    /* KPI CALCULATIONS */

    $totalBookings = (clone $query)->count();

    $totalAgreementValue = (clone $query)->sum('agreement_value');

    $totalBrokerage = (clone $query)->sum('current_effective_amount');

    $totalInvoice = (clone $query)->sum('total_invoice_amount');

    $totalReceived = (clone $query)->sum('total_received_amount');

    $pendingBrokerage = (clone $query)->sum('pending_brokerage_amount');


    /* PAYMENT STATUS */

    $pendingPayments = (clone $query)->where('payment_status','pending')->count();

    $partialPayments = (clone $query)->where('payment_status','partial')->count();

    $completedPayments = (clone $query)->where('payment_status','completed')->count();


    /* LEAD SOURCES */

    $leadSources = (clone $query)
    ->selectRaw('lead_source, COUNT(*) as total')
    ->groupBy('lead_source')
    ->pluck('total','lead_source');


    /* MONTHLY REVENUE */

    $monthlyRevenue = (clone $query)
    ->selectRaw('MONTH(booking_date) as month, SUM(current_effective_amount) as revenue')
    ->groupBy('month')
    ->pluck('revenue','month');


    /* TOP PROJECTS */

    $topProjects = (clone $query)
    ->join('projects','bookings.project_id','=','projects.id')
    ->selectRaw('projects.name,SUM(current_effective_amount) as brokerage')
    ->groupBy('projects.name')
    ->orderByDesc('brokerage')
    ->limit(5)
    ->get();


    /* TOP SALES */

    $topSales = (clone $query)
    ->join('users','bookings.sales_user_id','=','users.id')
    ->selectRaw('users.name,SUM(current_effective_amount) as revenue')
    ->groupBy('users.name')
    ->orderByDesc('revenue')
    ->limit(5)
    ->get();


    /* PENDING RECOVERY */

    $pendingRecovery = (clone $query)
    ->where('pending_brokerage_amount','>',0)
    ->with('project')
    ->orderByDesc('pending_brokerage_amount')
    ->limit(10)
    ->get();


    /* FILTER DATA */

    $clusterHeads = User::join('roles','users.role_id','=','roles.id')
    ->where('roles.name','Cluster Head')
    ->select('users.id','users.name')
    ->get();

    $srTls = User::join('roles','users.role_id','=','roles.id')
        ->where('roles.name','Sr. TL')
        ->select('users.id','users.name')
        ->get();

    $tls = User::join('roles','users.role_id','=','roles.id')
        ->where('roles.name','TL')
        ->select('users.id','users.name')
        ->get();

    $salesManagers = User::join('roles','users.role_id','=','roles.id')
        ->where('roles.name','FOS')
        ->select('users.id','users.name')
        ->get();

    $developers = Developer::select('id','name')->get();


    return view('dashboard-two',compact(

    'clusterHeads',
    'srTls',
    'tls',
    'salesManagers',
    'developers',

    'totalBookings',
    'totalAgreementValue',
    'totalBrokerage',
    'totalInvoice',
    'totalReceived',
    'pendingBrokerage',

    'pendingPayments',
    'partialPayments',
    'completedPayments',

    'leadSources',
    'monthlyRevenue',

    'topProjects',
    'topSales',

    'pendingRecovery'

    ));

    }



    /* RESET FILTERS */

    public function resetDashboard(){

    session()->forget([
    'cluster_head',
    'sr_tl',
    'tl',
    'sales_manager',
    'developer',
    'from_date',
    'to_date'
    ]);

    return redirect('/dashboard-two');

    }
}

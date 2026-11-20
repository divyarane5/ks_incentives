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
    use \App\Traits\UserHierarchyTrait;

    /**
     * RealtySuite home: one section per module the person can open (KeyLeads, KeyBookings,
     * KeyAccounts, KeyPeople, KeyEarn). Numbers inside a section also respect the page
     * permission behind them, so nobody sees figures they could not open themselves.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $mods = \App\Support\Modules::for($user);

        // Financial year (April to March) for bookings, collections and incentives.
        $thisFyStart = now()->month >= 4 ? now()->year : now()->year - 1;
        $fyStart = (int) $request->input('fy', $thisFyStart);
        $fyStart = max($thisFyStart - 4, min($thisFyStart, $fyStart));
        $from = \Carbon\Carbon::create($fyStart, 4, 1)->startOfDay();
        $to = \Carbon\Carbon::create($fyStart + 1, 3, 31)->endOfDay();
        $fyLabel = $fyStart.'-'.substr((string) ($fyStart + 1), -2);
        $fyOptions = collect(range($thisFyStart, $thisFyStart - 4))
            ->mapWithKeys(fn ($y) => [$y => $y.'-'.substr((string) ($y + 1), -2)])->all();

        $s = [];

        if (in_array('keyleads', $mods) && $user->usesCrm()) {
            $leads = fn () => \App\Models\Lead::query()->active()
                ->when(! $user->isAdmin(), fn ($q) => $q->whereIn('owner_id', $user->teamUserIds()));
            $s['keyleads'] = [
                'scope' => $user->isAdmin() ? 'Everyone' : ($user->role === User::ROLE_SALES ? 'Your leads' : 'You and your team'),
                'new_today' => $leads()->where('created_at', '>=', now()->startOfDay())->count(),
                'not_picked' => $leads()->whereNull('picked_at')->count(),
                'due_today' => $leads()->whereBetween('next_follow_up_at', [now()->startOfDay(), now()->endOfDay()])->count(),
                'overdue' => $leads()->where('next_follow_up_at', '<', now()->startOfDay())->count(),
                'open' => $leads()->count(),
            ];
        }

        if (in_array('keybookings', $mods)) {
            $sec = [];
            if ($user->can('booking-view')) {
                // Same rule as the bookings list: these roles see every booking, others their own team's.
                $seesAll = $user->hasAnyRole(['Superadmin', 'Super Admin', 'Admin', 'CRM', 'Accounts']);
                $teamIds = $seesAll ? null : $this->getAccessibleUserIds($user);
                $any = fn () => Booking::query()->when($teamIds !== null, fn ($q) => $q->whereIn('sales_user_id', $teamIds));
                $b = fn () => $any()->whereBetween('booking_date', [$from->toDateString(), $to->toDateString()]);
                $sec['bookings'] = [
                    'scope' => $seesAll ? 'All bookings' : 'You and your team',
                    'approved' => $b()->where('booking_confirm', 'approved')->count(),
                    'this_month' => $any()->where('booking_confirm', 'approved')
                        ->whereBetween('booking_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])->count(),
                    'waiting' => $any()->where(fn ($q) => $q->whereNull('booking_confirm')->orWhere('booking_confirm', 'pending'))->count(),
                    'cancelled' => $b()->where('booking_confirm', 'cancelled')->count(),
                    'agreement' => (float) $b()->where('booking_confirm', 'approved')->sum('agreement_value'),
                ];
            }
            if ($user->can('mandate_project-view') || $user->can('mandate-booking-view')) {
                $sec['mandate'] = [
                    'projects' => MandateProject::count(),
                    'bookings' => MandateBooking::count(),
                    'pending' => MandateBooking::where('booking_status', 'pending')->count(),
                    'partners' => ChannelPartner::count(),
                    'enquiries' => ClientEnquiry::count(),
                ];
            }
            $s['keybookings'] = $sec;
        }

        if (in_array('keyaccounts', $mods) && $user->can('payment-view')) {
            $a = fn () => Booking::query()->where('booking_confirm', 'approved')
                ->whereBetween('booking_date', [$from->toDateString(), $to->toDateString()]);
            $s['keyaccounts'] = [
                'brokerage' => (float) $a()->sum('current_effective_amount'),
                'invoiced' => (float) $a()->sum('total_invoice_amount'),
                'received' => (float) $a()->sum('total_received_amount'),
                // Money still to come in, across all years.
                'to_collect' => (float) Booking::where('booking_confirm', 'approved')->sum('pending_collection_amount'),
                'not_invoiced' => Booking::where('booking_confirm', 'approved')->where('invoice_raised', 0)->count(),
                'followups_due' => Booking::where('booking_confirm', 'approved')->where('payment_status', '!=', 'completed')
                    ->whereNotNull('payment_followup_date')->whereDate('payment_followup_date', '<=', now()->toDateString())->count(),
            ];
        }

        if (in_array('keypeople', $mods) && $user->can('user-view')) {
            $s['keypeople'] = [
                'active' => User::where('status', 'Active')->count(),
                'joined' => User::whereBetween('joining_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])->count(),
                'left' => User::whereBetween('leaving_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])->count(),
                'on_probation' => User::where('status', 'Active')->whereNull('confirm_date')->whereNotNull('joining_date')
                    ->where('joining_date', '>=', now()->subMonths(6)->toDateString())->count(),
            ];
        }

        if (in_array('keyearn', $mods)) {
            $fyKey = $fyLabel;
            $mine = \App\Models\IncentivePayout::where('user_id', $user->id)->where('financial_year', $fyKey);
            $sec = [
                'my_ytd' => (float) (clone $mine)->orderByDesc('quarter')->value('ytd_incentive'),
                'my_paid' => (float) (clone $mine)->sum('payable'),
                'my_quarters' => (clone $mine)->count(),
            ];
            if ($user->can('incentive-ledger-view')) {
                $all = \App\Models\IncentivePayout::where('financial_year', $fyKey);
                $sec['all_paid'] = (float) (clone $all)->sum('payable');
                $sec['all_people'] = (clone $all)->where('payable', '>', 0)->distinct('user_id')->count('user_id');
                $sec['last_quarter'] = (clone $all)->max('quarter');
            }
            $s['keyearn'] = $sec;
        }

        return view('home', [
            'mods' => $mods,
            's' => $s,
            'fyStart' => $fyStart,
            'fyLabel' => $fyLabel,
            'fyOptions' => $fyOptions,
        ]);
    }

    public function defaultDashboard()
    {
        $user = auth()->user();
        $role = $user->role;
        $businessUnit = $user->business_unit_id ?? null;

        $mandateBookingQuery   = MandateBooking::query();
        $mandateProjectQuery   = MandateProject::query();
        $channelPartnerQuery   = ChannelPartner::query();
        $clientEnquiryQuery    = ClientEnquiry::query();
        $mandateBookingBrokerageQuery = MandateBookingBrokerage::query();

        $totalRegistrations = $mandateBookingQuery->count();

        $eligibleBrokerageBookings = $mandateBookingBrokerageQuery
            ->where('is_eligible', 1)
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
        $user = auth()->user();

        /* STORE FILTERS */
        if ($request->isMethod('post')) {
            session([
                'cluster_head' => $request->cluster_head,
                'sr_tl' => $request->sr_tl,
                'tl' => $request->tl,
                'sales_manager' => $request->sales_manager,
                'developer' => $request->developer,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date
            ]);
        }

        /* GET FILTER VALUES */
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

        if ($developer) {
            $query->where('bookings.developer_id', $developer);
        }

        if ($sales_manager) {
            $query->where('sales_user_id', $sales_manager);
        }

        if ($from_date) {
            $query->whereDate('booking_date', '>=', $from_date);
        }

        if ($to_date) {
            $query->whereDate('booking_date', '<=', $to_date);
        }

        /* ✅ HIERARCHY LOGIC */

        if ($cluster_head) {
            // Cluster Head → Sr TL
            $srTlIds = User::where('reporting_manager_id', $cluster_head)->pluck('id');

            // Sr TL → TL
            $tlIds = User::whereIn('reporting_manager_id', $srTlIds)->pluck('id');

            // TL → Sales
            $salesIds = User::whereIn('reporting_manager_id', $tlIds)->pluck('id');

            $query->whereIn('sales_user_id', $salesIds);
        }

        if ($sr_tl) {
            // Sr TL → TL
            $tlIds = User::where('reporting_manager_id', $sr_tl)->pluck('id');

            // TL → Sales
            $salesIds = User::whereIn('reporting_manager_id', $tlIds)->pluck('id');

            $query->whereIn('sales_user_id', $salesIds);
        }

        if ($tl) {
            // TL → Sales
            $salesIds = User::where('reporting_manager_id', $tl)->pluck('id');

            $query->whereIn('sales_user_id', $salesIds);
        }

        /* KPI */
        $totalBookings = (clone $query)->count();
        $totalAgreementValue = (clone $query)->sum('agreement_value');
        $totalBrokerage = (clone $query)->sum('current_effective_amount');
        $totalInvoice = (clone $query)->sum('total_invoice_amount');
        $totalReceived = (clone $query)->sum('total_received_amount');
        $pendingBrokerage = (clone $query)->sum('pending_brokerage_amount');

        /* PAYMENT */
        $pendingPayments = (clone $query)->where('payment_status','pending')->count();
        $partialPayments = (clone $query)->where('payment_status','partial')->count();
        $completedPayments = (clone $query)->where('payment_status','completed')->count();

        /* LEAD SOURCE */
        $leadSources = (clone $query)
            ->selectRaw('lead_source, COUNT(*) as total')
            ->groupBy('lead_source')
            ->pluck('total','lead_source');

        /* MONTHLY */
        $monthlyRevenue = (clone $query)
            ->selectRaw('MONTH(booking_date) as month, SUM(current_effective_amount) as revenue')
            ->groupBy('month')
            ->pluck('revenue','month');

        /* TOP PROJECTS */
        $topProjects = (clone $query)
            ->join('projects','bookings.project_id','=','projects.id')
            ->selectRaw('projects.name, SUM(bookings.current_effective_amount) as brokerage')
            ->groupBy('projects.name')
            ->orderByDesc('brokerage')
            ->limit(5)
            ->get();

        /* TOP SALES */
        $topSales = (clone $query)
            ->join('users','bookings.sales_user_id','=','users.id')
            ->selectRaw('users.name, SUM(bookings.current_effective_amount) as revenue')
            ->groupBy('users.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        /* PENDING */
        $pendingRecovery = (clone $query)
            ->where('pending_brokerage_amount','>',0)
            ->with('project')
            ->orderByDesc('pending_brokerage_amount')
            ->limit(10)
            ->get();

        /* ✅ DROPDOWNS (CORRECT - USING ROLES TABLE) */

        $clusterHeads = User::join('roles','users.role_id','=','roles.id')
            ->where('roles.name','CH')
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

        return view('dashboard-two', compact(
            'clusterHeads','srTls','tls','salesManagers','developers',
            'totalBookings','totalAgreementValue','totalBrokerage',
            'totalInvoice','totalReceived','pendingBrokerage',
            'pendingPayments','partialPayments','completedPayments',
            'leadSources','monthlyRevenue',
            'topProjects','topSales','pendingRecovery',
            'cluster_head','sr_tl','tl','sales_manager','developer','from_date','to_date'
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

    return redirect('/incentive-dashboard-view');

    }
}

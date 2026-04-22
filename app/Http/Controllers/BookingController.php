<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Project;
use App\Models\Developer;
use App\Models\BusinessUnit;
use App\Traits\UserHierarchyTrait;
use App\Http\Requests\BookingRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Mail\BookingMail;
use Illuminate\Support\Facades\Mail;
use DB;
//use App\Services\BookingRevenueService;
use App\Services\BrokerageCalculationService;

class BookingController extends Controller
{
    use UserHierarchyTrait; // ✅ MUST ADD THIS
    function __construct()
    {
        $this->middleware('permission:booking-view', ['only' => ['index']]);
        $this->middleware('permission:booking-create', ['only' => ['create','store']]);
        $this->middleware('permission:booking-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:booking-delete', ['only' => ['destroy']]);
        $this->middleware('permission:booking-status-update')->only([
            'updateStatus','updateIStatus','updateBStatus'
        ]);

    }
    
    
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user(); 
            $accessibleUserIds = $this->getAccessibleUserIds($user);
            $data = Booking::withTrashed()
                ->with([
                    'project',
                    'developer',
                    'user.reportingManager.reportingManager.reportingManager'
                ])
                ->whereIn('sales_user_id', $accessibleUserIds) // ✅ IMPORTANT LINE
                ->select('bookings.*');

            return DataTables::of($data)

                ->addColumn('project_name', function ($row) {
                    return optional($row->project)->name ?? '-';
                })

                ->addColumn('developer_name', function ($row) {
                    return optional($row->developer)->name ?? '-';
                })

                ->addColumn('booking_amount', fn($row) => number_format($row->booking_amount ?? 0, 0))
                ->addColumn('agreement_value', fn($row) => number_format($row->agreement_value ?? 0, 0))
                ->addColumn('current_effective_amount', fn($row) => number_format($row->current_effective_amount ?? 0, 0))
                ->addColumn('additional_kicker', fn($row) => number_format($row->additional_kicker ?? 0, 0))
                ->addColumn('passback', fn($row) => number_format($row->passback ?? 0, 0))
                ->addColumn('final_revenue', fn($row) => number_format($row->final_revenue ?? 0, 0))
                ->addColumn('total_paid_amount', fn($row) => number_format($row->total_paid_amount ?? 0, 0))
                ->addColumn('pending_amount', fn($row) => number_format($row->pending_amount ?? 0, 0))
                ->addColumn('total_invoice_percent', function ($row) {
                    return number_format($row->total_invoice_percent ?? 0, 2) . '%';
                })

                ->addColumn('total_invoice_amount', function ($row) {
                    return number_format($row->total_invoice_amount ?? 0, 0);
                })

                ->addColumn('total_received_amount', function ($row) {
                    return number_format($row->total_received_amount ?? 0, 0);
                })

                ->addColumn('pending_brokerage_percent', function ($row) {
                    return number_format($row->pending_brokerage_percent ?? 0, 2) . '%';
                })

                ->addColumn('pending_brokerage_amount', function ($row) {
                    return number_format($row->pending_brokerage_amount ?? 0, 0);
                })

                ->addColumn('payment_status', function ($row) {

                    if($row->payment_status == 'completed'){
                        return '<span class="badge bg-success">Completed</span>';
                    }

                    if($row->payment_status == 'partial'){
                        return '<span class="badge bg-warning">Partial</span>';
                    }

                    return '<span class="badge bg-danger">Pending</span>';
                })
                ->addColumn('base_brokerage_percent', function ($row) {
                    return number_format($row->base_brokerage_percent ?? 0, 2) . '%';
                })

                ->addColumn('site_increment_percent', function ($row) {
                    $increment = ($row->site_ladder_percent ?? 0) - ($row->base_brokerage_percent ?? 0);
                    return number_format($increment, 2) . '%';
                })

                ->addColumn('aop_ladder_percent', function ($row) {
                    return number_format($row->aop_ladder_percent ?? 0, 2) . '%';
                })

                ->addColumn('total_brokerage_percent', function ($row) {

                    $base = $row->base_brokerage_percent ?? 0;

                    // ✅ FIXED
                    $site_increment = $row->site_ladder_percent ?? 0;

                    $aop = $row->aop_ladder_percent ?? 0;

                    $total = $row->total_brokerage_percent ?? 0;

                    $tooltip = "
                        Base: ".number_format($base,2)." %<br>
                        Site Increment: ".number_format($site_increment,2)." %<br>
                        AOP: ".number_format($aop,2)." %
                    ";

                    return '<span data-bs-toggle="tooltip" title="'.$tooltip.'">
                                '.number_format($total,2).' %
                            </span>';
                })
                //->rawColumns(['total_brokerage_percent'])
                // ->addColumn('sales_manager', function ($row) {
                //     return optional($row->user)->name ?? '-';
                // })

                // ->addColumn('tl', function ($row) {
                //     return optional(optional($row->user)->reportingManager)->name ?? '-';
                // })

                // ->addColumn('sr_tl', function ($row) {
                //     return optional(
                //         optional(optional($row->user)->reportingManager)->reportingManager
                //     )->name ?? '-';
                // })

                // ->addColumn('cluster_head', function ($row) {
                //     return optional(
                //         optional(
                //             optional(optional($row->user)->reportingManager)
                //                 ->reportingManager
                //         )->reportingManager
                //     )->name ?? '-';
                // })
                ->addColumn('team_hierarchy', function ($row) {

                    $salesManager = optional($row->user)->name ?? '-';
                    $tl = optional(optional($row->user)->reportingManager)->name ?? '-';
                    $srTl = optional(optional(optional($row->user)->reportingManager)->reportingManager)->name ?? '-';
                    $clusterHead = optional(
                        optional(
                            optional(optional($row->user)->reportingManager)
                            ->reportingManager
                        )->reportingManager
                    )->name ?? '-';

                    $fullHierarchy = "
                    <strong>SM:</strong> {$salesManager}<br>
                    <strong>TL:</strong> {$tl}<br>
                    <strong>Sr TL:</strong> {$srTl}<br>
                    <strong>CH:</strong> {$clusterHead}
                    ";

                    return '
                    <span 
                        class="badge bg-label-primary team-hover"
                        data-bs-toggle="tooltip"
                        data-bs-html="true"
                        title="'.$fullHierarchy.'"
                    >
                        '.$salesManager.' <i class="bx bx-group"></i>
                    </span>';
                })
                ->addColumn('booking_confirm', function ($row) {

                    return '<select onchange="updateBStatus(this, ' . $row->id . ')">
                        <option value="pending" ' . ($row->booking_confirm == 'pending' ? 'selected' : '') . '>Pending</option>
                        <option value="approved" ' . ($row->booking_confirm == 'approved' ? 'selected' : '') . '>Approved</option>
                        <option value="cancelled" ' . ($row->booking_confirm == 'cancelled' ? 'selected' : '') . '>Cancelled</option>
                    </select>';

                })

                // ->addColumn('registration_confirm', function ($row) {
                //     return '<input type="checkbox"
                //             onchange="updateStatus(this,' . $row->id . ')"
                //             ' . ($row->registration_confirm ? 'checked' : '') . '>';
                // })

                // ->addColumn('invoice_raised', function ($row) {
                //     return '<input type="checkbox"
                //             onchange="updateIStatus(this,' . $row->id . ')"
                //             ' . ($row->invoice_raised ? 'checked' : '') . '>';
                // })

                ->addColumn('action', function ($row) {

                    return '
                    <div class="dropdown">
                        <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>

                        <div class="dropdown-menu">

                            <a class="dropdown-item" href="' . route('booking.edit', $row->id) . '">
                                <i class="bx bx-edit-alt me-1"></i> Edit
                            </a>

                            <a class="dropdown-item add-payment"
                                data-id="'.$row->id.'"
                                data-agreement="'.$row->agreement_value.'"
                                data-percent="'.$row->total_brokerage_percent.'"
                                data-brokerage="'.$row->current_effective_amount.'"
                                data-status="'.$row->payment_status.'"
                                data-bs-toggle="modal"
                                data-bs-target="#addPaymentModal">

                                <i class="bx bx-money me-1"></i> Add Payment
                            </a>

                        </div>
                    </div>';
                })
                ->rawColumns([
                     'team_hierarchy',
                        'payment_status',
                        'booking_confirm',
                        'registration_confirm',
                        'invoice_raised',
                        'total_brokerage_percent',
                        'action'
                ])

                ->make(true);
        }

        return view('booking.index');
    }

    public function create()
    {
        $developer_name = Developer::all();
        $project_name   = Project::all();

        // Get Business Unit ID where code = KREA
        $businessUnit = BusinessUnit::where('code', 'KREA')->first();

        // Fetch users belonging to that business unit
        $salesManagers = User::where('business_unit_id', $businessUnit->id ?? null)
                            ->get();

        return view('booking.create', compact(
            'developer_name',
            'project_name',
            'salesManagers'
        ));
    }
    public function store(BookingRequest $request, BrokerageCalculationService $service)
    {
        $booking = new Booking();

        // Basic Details
        $booking->booking_date = $request->booking_date;
        $booking->client_name = $request->client_name;
        $booking->client_contact = $request->client_contact;
        $booking->lead_source = $request->lead_source;

        $booking->project_id = $request->project_id;
        $booking->developer_id = $request->developer_id;
        $booking->tower = $request->tower ?? '';
        $booking->wing = $request->wing ?? '';
        $booking->flat_no = $request->flat_no ?? '';
        $booking->configuration = $request->configuration ?? '';

        // Financial
        $booking->booking_amount = $request->booking_amount ?? 0;   
        $booking->agreement_value = $request->agreement_value ?? 0;

        $booking->additional_kicker = $request->additional_kicker ?? 0;
        $booking->passback = $request->passback ?? 0;

        // Registration
        $booking->registration_date = $request->registration_date;
        $booking->remark = $request->remark;
        $booking->sales_user_id = $request->sales_user_id;

        $booking->created_by = auth()->id();

        $booking->save();

        // Recalculate ladders
        $service->recalculateAll($booking->project_id);

        return redirect()
            ->route('booking.index')
            ->with('success', 'Booking Added Successfully');
    }

    public function edit($id)
    {
        $booking = Booking::findOrFail($id);

        $developer_name = Developer::all();
        $project_name   = Project::all();
        $salesManagers  = User::all();   // <-- FIXED

        return view('booking.edit', compact(
            'booking',
            'developer_name',
            'project_name',
            'salesManagers'
        ));
    }
    public function update(BookingRequest $request, $id, BrokerageCalculationService $service)
    {
        $booking = Booking::findOrFail($id);

        $booking->update([
            'project_id'        => $request->project_id,
            'developer_id'      => $request->developer_id,
            'client_name'       => $request->client_name,
            'booking_date'      => $request->booking_date,
            'client_contact'    => $request->client_contact,
            'lead_source'       => $request->lead_source,
            'tower' => $request->tower ?? '',
            'wing' => $request->wing ?? '',
            'flat_no' => $request->flat_no ?? '',
            'configuration' => $request->configuration ?? '',
            'booking_amount'    => $request->booking_amount,
            'agreement_value'   => $request->agreement_value,
            'passback'          => $request->passback,
            'additional_kicker' => $request->additional_kicker,
            'registration_date' => $request->registration_date,
            'sales_user_id'     => $request->sales_user_id,
            'remark'            => $request->remark,
        ]);
        $service = new BrokerageCalculationService();
        $service->recalculateAll($booking->project_id);
        //dd($booking->project_id);
       // dd(Booking::where('project_id',$booking->project_id)->count());
       
        return redirect()
            ->route('booking.index')
            ->with('success', 'Booking Updated Successfully');
    }

    public function destroy($id)
    {
        Booking::where('id', $id)->delete();
        return redirect()->route('booking.index')->with('success', 'Booking Deleted Successfully');
    }

    public function show($id)
    {
        $booking = Booking::find($id);
        $user = DB::table('users')
                    ->select('users.name as u_name','users.id as u_id','designations.name as d_name','users.mobile')
                   ->join('designations', 'users.designation_id','=','designations.id','inner')
	               ->where('users.id',$booking->created_by)
	               ->first();

        return view('booking.show', compact('id', 'booking','user'));

    }

    public function sendBookingMail($id){
        $booking = Booking::find($id);
        //echo $booking->created_by; exit;
        $user = DB::table('users')
                    ->select('users.name as u_name','users.id as u_id','users.email as u_email','designations.name as d_name','users.mobile')
                   ->join('designations', 'users.designation_id','=','designations.id','left')
	               ->where('users.id',$booking->created_by)
	               ->first();
                //   print_r($user); exit;
        $arr = [
            'id' => $id,
            'booking' => $booking,
            'user' => $user
        ];
        
        Mail::to('keystonedivya@gmail.com')->send(new BookingMail($arr)); //$approvalTo->email
        return redirect()->route('booking.index')->with('success', 'Mail Sent Successfully');
    }

    public function updateStatus(Request $request)
    {
        $bookingId = $request->id;
        $registration_confirm = $request->registration_confirm;

        Booking::where('id', $bookingId)->update(['registration_confirm' => $registration_confirm]);

        return response()->json(['message' => 'The booking status updated successfully']);
    }
    public function updateIStatus(Request $request)
    {
        $bookingId = $request->id;
        $invoice_raised = $request->invoice_raised;

        Booking::where('id', $bookingId)->update(['invoice_raised' => $invoice_raised]);

        return response()->json(['message' => 'The invoice status updated successfully']);
    }
    public function updateBStatus(Request $request)
    {
        $booking = Booking::findOrFail($request->id);

        $booking->booking_confirm = $request->booking_confirm;
        $booking->save();

        if ($request->booking_confirm === 'approved') {

            app(\App\Services\BrokerageCalculationService::class)
                ->recalculateAll($booking->project_id);

        } else {

            $this->resetBrokerage($booking);

            app(\App\Services\BrokerageCalculationService::class)
                ->recalculateAll($booking->project_id);
        }

        return response()->json([
            'message' => 'Booking status updated successfully'
        ]);
    }
    private function resetBrokerage($booking)
    {
        $booking->base_brokerage_percent = 0;
        $booking->site_ladder_percent = 0;
        $booking->aop_ladder_percent = 0;
        $booking->total_brokerage_percent = 0;
        $booking->current_effective_amount = 0;
        $booking->final_revenue = 0;
        $booking->amount_receivable = 0;
        $booking->tds_amount = 0;

        $booking->save();
    }
} 

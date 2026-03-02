<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Project;
use App\Models\Developer;
use App\Models\BusinessUnit;
use App\Http\Requests\BookingRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Mail\BookingMail;
use Illuminate\Support\Facades\Mail;
use DB;
use App\Services\BookingRevenueService;
use App\Services\BrokerageCalculationService;

class BookingController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:booking-view', ['only' => ['index']]);
        $this->middleware('permission:booking-create', ['only' => ['create','store']]);
        $this->middleware('permission:booking-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:booking-delete', ['only' => ['destroy']]);

    }
    


    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Booking::withTrashed()
                ->with([
                    'project',
                    'developer',
                    'user.reportingManager.reportingManager.reportingManager'
                ])
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
                    return number_format($row->total_brokerage_percent ?? 0, 2) . '%';
                })

                ->addColumn('sales_manager', function ($row) {
                    return optional($row->user)->name ?? '-';
                })

                ->addColumn('tl', function ($row) {
                    return optional(optional($row->user)->reportingManager)->name ?? '-';
                })

                ->addColumn('sr_tl', function ($row) {
                    return optional(
                        optional(optional($row->user)->reportingManager)->reportingManager
                    )->name ?? '-';
                })

                ->addColumn('cluster_head', function ($row) {
                    return optional(
                        optional(
                            optional(optional($row->user)->reportingManager)
                                ->reportingManager
                        )->reportingManager
                    )->name ?? '-';
                })

                // ->addColumn('booking_confirm', function ($row) {
                //     return '<input type="checkbox"
                //             onchange="updateBStatus(this,' . $row->id . ')"
                //             ' . ($row->booking_confirm ? 'checked' : '') . '>';
                // })

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
                        </div>
                    </div>';
                })

                ->rawColumns([
                    'booking_confirm',
                    'registration_confirm',
                    'invoice_raised',
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
        // dd($request->project_id);
        // exit;
        // echo "ss"; 
        // print_r($request->all()); exit;
        $calc = $service->calculate(
            $request->project_id,
            $request->developer_id,
            $request->agreement_value,
            $request->booking_date
        );

        $booking = new Booking();

        // Basic Details
        $booking->booking_date = $request->booking_date;
        $booking->client_name = $request->client_name;
        $booking->client_contact = $request->client_contact;
        $booking->lead_source = $request->lead_source;

        $booking->project_id = $request->project_id;
        $booking->developer_id = $request->developer_id;
        $booking->tower = $request->tower;
        $booking->wing = $request->wing;
        $booking->flat_no = $request->flat_no;
        $booking->configuration = $request->configuration;

        // Financial Values
        $booking->booking_amount = $request->booking_amount;
        $booking->agreement_value = $request->agreement_value;

        // Brokerage Snapshot
        $booking->base_brokerage_percent = $calc['base_percent'];
        $booking->site_ladder_percent = $calc['site_percent'];
        $booking->aop_ladder_percent = $calc['aop_percent'];
        $booking->total_brokerage_percent = $calc['total_percent'];
        $booking->current_effective_amount = $calc['brokerage_amount'];

        $booking->additional_kicker = $request->additional_kicker ?? 0;
        $booking->passback = $request->passback ?? 0;

        $booking->final_revenue =
            $booking->current_effective_amount
            + $booking->additional_kicker
            - $booking->passback;

        // Registration & Status
        $booking->registration_date = $request->registration_date;
        $booking->remark = $request->remark;
        $booking->sales_user_id = $request->sales_user_id;

        $booking->created_by = auth()->id();

        $booking->save();

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
    public function update(BookingRequest $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $booking->update([
            'project_id'        => $request->project_id,
            'developer_id'      => $request->developer_id,
            'client_name'       => $request->client_name,
            'booking_date'      => $request->booking_date,
            'client_contact'    => $request->client_contact,
            'lead_source'       => $request->lead_source,
            'configuration'     => $request->configuration,
            'flat_no'           => $request->flat_no,
            'wing'              => $request->wing,
            'tower'             => $request->tower,
            'booking_amount'    => $request->booking_amount,
            'agreement_value'   => $request->agreement_value,
            'passback'          => $request->passback,
            'additional_kicker' => $request->additional_kicker,
            'registration_date' => $request->registration_date,
            'sales_user_id'     => $request->sales_user_id,
            'remark'            => $request->remark,
        ]);

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
        $bookingId = $request->id;
        $booking_confirm = $request->booking_confirm;

        Booking::where('id', $bookingId)->update(['booking_confirm' => $booking_confirm]);

        return response()->json(['message' => 'The booking status updated successfully']);
    }
} 

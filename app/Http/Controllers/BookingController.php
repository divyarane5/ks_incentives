<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Http\Requests\BookingRequest;
use DataTables;
use Illuminate\Http\Request;
use App\Mail\BookingMail;
use Illuminate\Support\Facades\Mail;
use DB;
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
            $data = Booking::all();
            return DataTables::of($data)
                ->addColumn('project_name', function ($row) {
                    return $row->project_name;
                })
                ->addColumn('developer_name', function ($row) {
                    return $row->developer_name;
                })
                ->addColumn('developer_email', function ($row) {
                    return $row->developer_email;
                })
                ->addColumn('configuration', function ($row) {
                    return $row->configuration;
                })
                ->addColumn('tower', function ($row) {
                    return $row->tower;
                })
                ->addColumn('booking_date', function ($row) {
                    return $row->booking_date;
                })
                ->addColumn('flat_no', function ($row) {
                    return $row->flat_no;
                })
                ->addColumn('wing', function ($row) {
                    return $row->wing;
                })
                ->addColumn('sales_person', function ($row) {
                    return $row->sales_person;
                })
                ->addColumn('sourcing_manager', function ($row) {
                    return $row->sourcing_manager;
                })
                ->addColumn('created_at', function ($row) {
                    return date("d-m-Y", strtotime($row->created_at));
                })
                ->addColumn('updated_at', function ($row) {
                    return ($row->updated_at != "") ? date("d-m-Y", strtotime($row->updated_at)) : '-';
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->can('booking-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('booking.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('booking-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteBooking('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('booking.destroy', $row->id).'" method="POST" class="d-none">
                                        '.csrf_field().'
                                        '.method_field('delete').'
                                    </form>';
                    }
                    if (auth()->user()->can('booking-view')) {
                        $actions .= '<a class="dropdown-item" target=”_blank”  href="'.route('booking.show', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> View</a>';
                    }
                    if (auth()->user()->can('booking-view')) {
                        $actions .= '<a class="dropdown-item" target=”_blank”  href="'.url('send_booking_mail/'.$row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Send Email</a>';
                    }

                    if (!empty($actions)) {
                        return '<div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                        '.$actions.'
                                        </div>
                                    </div>';
                    }

                    return '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('booking.index');
    }

    public function create()
    {
        return view('booking.create');
    }

    public function store(BookingRequest $request)
    {
        //create booking
        $booking = new Booking();
        $booking->project_name = $request->input('project_name');
        $booking->developer_name = $request->input('developer_name');
        $booking->developer_email = $request->input('developer_email');
        $booking->client_name = $request->input('client_name');
        $booking->booking_date = $request->input('booking_date');
        $booking->configuration = $request->input('configuration');
        $booking->flat_no = $request->input('flat_no');
        $booking->wing = $request->input('wing');
        $booking->tower = $request->input('tower');
        $booking->sales_person = $request->input('sales_person');
        $booking->sourcing_manager = $request->input('sourcing_manager');
        $booking->save();

        return redirect()->route('booking.index')->with('success', 'Booking Added Successfully');
    }

    public function edit($id)
    {
        $booking = Booking::find($id);
        return view('booking.edit', compact('id', 'booking'));
    }

    public function update(BookingRequest $request, $id)
    {
        $booking = Booking::find($id);
        $booking->project_name = $request->input('project_name');
        $booking->developer_name = $request->input('developer_name');
        $booking->developer_email = $request->input('developer_email');
        $booking->client_name = $request->input('client_name');
        //$booking->booking_date = $request->input('booking_date');
        $booking->configuration = $request->input('configuration');
        $booking->flat_no = $request->input('flat_no');
        $booking->wing = $request->input('wing');
        $booking->tower = $request->input('tower');
        $booking->sales_person = $request->input('sales_person');
        $booking->sourcing_manager = $request->input('sourcing_manager');
        $booking->save();

        return redirect()->route('booking.index')->with('success', 'Booking Updated Successfully');
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
        $user = DB::table('users')
                    ->select('users.name as u_name','users.id as u_id','users.email as u_email','designations.name as d_name','users.mobile')
                   ->join('designations', 'users.designation_id','=','designations.id','inner')
	               ->where('users.id',$booking->created_by)
	               ->first();
        $arr = [
            'id' => $id,
            'booking' => $booking,
            'user' => $user
        ];
        Mail::to('divya.rane@homebazaar.com')->send(new BookingMail($arr)); //$approvalTo->email
        return redirect()->route('booking.index')->with('success', 'Mail Sent Successfully');
    }
}

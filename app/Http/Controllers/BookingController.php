<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Project;
use App\Models\Developer;
use App\Http\Requests\BookingRequest;
use Yajra\DataTables\Facades\DataTables;
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
                ->addColumn('booking_date', function ($row) {
                    return $row->booking_date;
                })
                ->addColumn('client_name', function ($row) {
                    return $row->client_name;
                })
                ->addColumn('project_name', function ($row) {
                    $project = DB::table('projects')
                    ->select('projects.name')
                    ->where('projects.id',$row->project_id)
                    ->first();
                    return $project->name;
                })
                ->addColumn('developer_name', function ($row) {
                    $developer = DB::table('developers')
                    ->select('developers.name')
                    ->where('developers.id',$row->developer_id)
                    ->first();
                    return $developer->name;
                })
                
                ->addColumn('agreement_value', function ($row) {
                    return "Rs. ".number_format($row->agreement_value);
                })
                
                ->addColumn('brokerage', function ($row) {
                    
                        return $row->brokerage." %";
                   
                })
                ->addColumn('project_brokerage', function ($row) {
                    // $t_bookings = DB::table('project_ladders')
                    // ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','right')
                    // ->select('project_ladders.*','bookings.id')
                    // ->where('project_ladders.project_id',$row->project_id)
                    // ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                    // ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                    // //->groupBy('project_ladders.*','bookings.id')
                    // ->get();    
                    $t_bookings = DB::table('bookings')
                    ->leftJoin('project_ladders', 'bookings.project_id', '=', 'project_ladders.project_id')
                    ->select('bookings.client_name')
                    ->where('bookings.project_id',$row->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                    ->groupBy('bookings.client_name')
                    ->get();    
                    $project_ladder = DB::table('project_ladders')
                                    ->select('project_ladders.*')
                                    ->where('project_ladders.s_booking','<=',count($t_bookings))
                                    ->where('project_ladders.e_booking','>=',count($t_bookings))
                                    ->where('project_ladders.project_id',$row->project_id)
                                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                                   // ->orderBy('project_ladders.booking','desc')
                                    ->first();
                    if(!empty($project_ladder)){
                        //yes
                        return $project_ladder->ladder." %";
                    }else{
                        //no
                        return "Not Eigible";
                    }
                })
                ->addColumn('aop_brokerage', function ($row) {
                    $t_agreementv = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id','bookings.agreement_value')
                    ->where('project_ladders.project_id',$row->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->sum('bookings.agreement_value');
                    
                    $developer_ladder = DB::table('developer_ladders')
                    ->select('developer_ladders.*')
                    ->where('developer_ladders.aop','>=',$t_agreementv)
                    ->where('developer_ladders.developer_id',$row->developer_id)
                    ->whereDate('developer_ladders.aop_s_date', '<', $row->booking_date)
                    ->whereDate('developer_ladders.aop_e_date', '<', $row->booking_date)
                    ->first();
                   // $total = $t_agreementv->sum('aop');
                    if(!empty($developer_ladder)){
                        //yes
                        return $developer_ladder->ladder." %";
                    }else{
                        //no
                        return "Not Eigible";
                    }
                    
                })
                ->addColumn('total_brokerage', function ($row) {
                    // $brokerage = DB::table('projects')
                    // ->select('projects.brokerage')
                    // ->where('projects.id',$row->project_id)
                    // ->first();
                    $t_bookings = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id')
                    ->where('project_ladders.project_id',$row->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->get();
                    $project_ladder = DB::table('project_ladders')
                                    ->select('project_ladders.*')
                                    ->where('project_ladders.s_booking','<=',count($t_bookings))
                                     ->where('project_ladders.e_booking','>=',count($t_bookings))
                                    ->where('project_ladders.project_id',$row->project_id)
                                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                                   // ->orderBy('project_ladders.booking','desc')
                                    ->first();
                    $t_agreementv = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id','bookings.agreement_value')
                    ->where('project_ladders.project_id',$row->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->sum('bookings.agreement_value');
                    
                    $developer_ladder = DB::table('developer_ladders')
                    ->select('developer_ladders.*')
                    ->where('developer_ladders.aop','>=',$t_agreementv)
                    ->where('developer_ladders.developer_id',$row->developer_id)
                    ->whereDate('developer_ladders.aop_s_date', '<', $row->booking_date)
                    ->whereDate('developer_ladders.aop_e_date', '<', $row->booking_date)
                    ->first();
                    if(!empty($developer_ladder)){
                        //yes
                        return ($developer_ladder->ladder+$project_ladder->ladder + $row->brokerage)." %";
                    }else{
                        if(!empty($project_ladder)){
                            //yes
                            return ($project_ladder->ladder + $row->brokerage)." %";
                        }else{
                            //no
                            return $row->brokerage." %";
                        }
                    }
                    
                       
                })
                ->addColumn('base_revenue', function ($row) {
                    // $brokerage = DB::table('projects')
                    // ->select('projects.brokerage')
                    // ->where('projects.id',$row->project_id)
                    // ->first();
                    $t_bookings = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id')
                    ->where('project_ladders.project_id',$row->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->get();
                    $project_ladder = DB::table('project_ladders')
                                    ->select('project_ladders.*')
                                    ->where('project_ladders.s_booking','<=',count($t_bookings))
                                    ->where('project_ladders.e_booking','>=',count($t_bookings))
                                    ->where('project_ladders.project_id',$row->project_id)
                                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                                   // ->orderBy('project_ladders.booking','desc')
                                    ->first();
                    $t_agreementv = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id','bookings.agreement_value')
                    ->where('project_ladders.project_id',$row->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->sum('bookings.agreement_value');
                    
                    $developer_ladder = DB::table('developer_ladders')
                    ->select('developer_ladders.*')
                    ->where('developer_ladders.aop','>=',$t_agreementv)
                    ->where('developer_ladders.developer_id',$row->developer_id)
                    ->whereDate('developer_ladders.aop_s_date', '<', $row->booking_date)
                    ->whereDate('developer_ladders.aop_e_date', '<', $row->booking_date)
                    ->first();
                    if(!empty($developer_ladder)){
                        //yes
                        $revenue = ((($developer_ladder->ladder+$project_ladder->ladder + $row->brokerage)/100)* $row->agreement_value);
                    }else{
                        if(!empty($project_ladder)){
                            //yes
                            $revenue = ((($project_ladder->ladder + $row->brokerage)/100)* $row->agreement_value);
                        }else{
                            //no
                            $revenue = (($row->brokerage/100)* $row->agreement_value);
                        }
                    }
                    
                    return "Rs. ".number_format($revenue);
                })
                ->addColumn('tds', function ($row) {
                    return "2 %";
                })
                ->addColumn('net_base_revenue', function ($row) {
                    // $brokerage = DB::table('projects')
                    // ->select('projects.brokerage')
                    // ->where('projects.id',$row->project_id)
                    // ->first();
                    $t_bookings = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id')
                    ->where('project_ladders.project_id',$row->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->get();
                    $project_ladder = DB::table('project_ladders')
                                    ->select('project_ladders.*')
                                    ->where('project_ladders.s_booking','<=',count($t_bookings))
                                    ->where('project_ladders.e_booking','>=',count($t_bookings))
                                    ->where('project_ladders.project_id',$row->project_id)
                                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                                   // ->orderBy('project_ladders.booking','desc')
                                    ->first();
                    $t_agreementv = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id','bookings.agreement_value')
                    ->where('project_ladders.project_id',$row->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->sum('bookings.agreement_value');
                    
                    $developer_ladder = DB::table('developer_ladders')
                    ->select('developer_ladders.*')
                    ->where('developer_ladders.aop','>=',$t_agreementv)
                    ->where('developer_ladders.developer_id',$row->developer_id)
                    ->whereDate('developer_ladders.aop_s_date', '<', $row->booking_date)
                    ->whereDate('developer_ladders.aop_e_date', '<', $row->booking_date)
                    ->first();
                    if(!empty($developer_ladder)){
                        //yes
                        $revenue = ((($developer_ladder->ladder+$project_ladder->ladder + $row->brokerage)/100)* $row->agreement_value);
                    }else{
                        if(!empty($project_ladder)){
                            //yes
                            $revenue = ((($project_ladder->ladder + $row->brokerage)/100)* $row->agreement_value);
                        }else{
                            //no
                            $revenue = (($row->brokerage/100)* $row->agreement_value);
                        }
                    }
                    
                    return "Rs. ".number_format($revenue * 0.98);
                })
                ->addColumn('passback', function ($row) {
                    return "Rs. ".number_format($row->passback);
                })
                ->addColumn('actual_revenue', function ($row) {
                    // $brokerage = DB::table('projects')
                    // ->select('projects.brokerage')
                    // ->where('projects.id',$row->project_id)
                    // ->first();
                    $t_bookings = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id')
                    ->where('project_ladders.project_id',$row->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->get();
                    $project_ladder = DB::table('project_ladders')
                                    ->select('project_ladders.*')
                                    ->where('project_ladders.s_booking','<=',count($t_bookings))
                                    ->where('project_ladders.e_booking','>=',count($t_bookings))
                                    ->where('project_ladders.project_id',$row->project_id)
                                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                                   // ->orderBy('project_ladders.booking','desc')
                                    ->first();
                    $t_agreementv = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id','bookings.agreement_value')
                    ->where('project_ladders.project_id',$row->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->sum('bookings.agreement_value');
                    
                    $developer_ladder = DB::table('developer_ladders')
                    ->select('developer_ladders.*')
                    ->where('developer_ladders.aop','>=',$t_agreementv)
                    ->where('developer_ladders.developer_id',$row->developer_id)
                    ->whereDate('developer_ladders.aop_s_date', '<', $row->booking_date)
                    ->whereDate('developer_ladders.aop_e_date', '<', $row->booking_date)
                    ->first();
                    if(!empty($developer_ladder)){
                        //yes
                        $revenue = ((($developer_ladder->ladder+$project_ladder->ladder + $row->brokerage)/100)* $row->agreement_value);
                    }else{
                        if(!empty($project_ladder)){
                            //yes
                            $revenue = ((($project_ladder->ladder + $row->brokerage)/100)* $row->agreement_value);
                        }else{
                            //no
                            $revenue = (($row->brokerage/100)* $row->agreement_value);
                        }
                    }
                    
                    $actual_revenue = ($revenue * 0.98) - $row->passback;
                    return "Rs. ".number_format($actual_revenue);
                })
               
                ->addColumn('additional_kicker', function ($row) {
                    return "Rs. ".number_format($row->additional_kicker);
                })
                ->addColumn('total_revenue', function ($row) {
                    // $brokerage = DB::table('projects')
                    // ->select('projects.brokerage')
                    // ->where('projects.id',$row->project_id)
                    // ->first();
                    $t_bookings = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id')
                    ->where('project_ladders.project_id',$row->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->get();
                    $project_ladder = DB::table('project_ladders')
                                    ->select('project_ladders.*')
                                    ->where('project_ladders.s_booking','<=',count($t_bookings))
                                    ->where('project_ladders.e_booking','>=',count($t_bookings))
                                    ->where('project_ladders.project_id',$row->project_id)
                                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                                   // ->orderBy('project_ladders.booking','desc')
                                    ->first();
                    $t_agreementv = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id','bookings.agreement_value')
                    ->where('project_ladders.project_id',$row->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $row->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $row->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->sum('bookings.agreement_value');
                    
                    $developer_ladder = DB::table('developer_ladders')
                    ->select('developer_ladders.*')
                    ->where('developer_ladders.aop','>=',$t_agreementv)
                    ->where('developer_ladders.developer_id',$row->developer_id)
                    ->whereDate('developer_ladders.aop_s_date', '<', $row->booking_date)
                    ->whereDate('developer_ladders.aop_e_date', '<', $row->booking_date)
                    ->first();
                    if(!empty($developer_ladder)){
                        //yes
                        $revenue = ((($developer_ladder->ladder+$project_ladder->ladder + $row->brokerage)/100)* $row->agreement_value);
                    }else{
                        if(!empty($project_ladder)){
                            //yes
                            $revenue = ((($project_ladder->ladder + $row->brokerage)/100)* $row->agreement_value);
                        }else{
                            //no
                            $revenue = (($row->brokerage/100)* $row->agreement_value);
                        }
                    }
                    
                    $actual_revenue = ($revenue * 0.98) - $row->passback;
                    return "Rs. ".number_format(($actual_revenue)+$row->additional_kicker);
                })
                ->addColumn('sales_person', function ($row) {
                    $user = DB::table('users')
                    ->select('users.name','reporting_user_id')
                    ->where('users.id',$row->sales_person)
                    ->first();
                    return $user->name;
                })
                
                ->addColumn('team_leader', function ($row) {
                    $user = DB::table('users')
                    ->select('users.name','reporting_user_id')
                    ->where('users.id',$row->sales_person)
                    ->first();
                    
                    $ruser = DB::table('users')
                    ->select('users.name','reporting_user_id')
                    ->where('users.id',$user->reporting_user_id)
                    ->first();
                    if(!empty($ruser)){ $reporting = $ruser->name; }else{ $reporting = "-"; }
                    return $reporting;
                })
                ->addColumn('sr_team_leader', function ($row) {
                    $user = DB::table('users')
                    ->select('users.name','reporting_user_id')
                    ->where('users.id',$row->sales_person)
                    ->first();
                    
                    $ruser = DB::table('users')
                    ->select('users.name','reporting_user_id')
                    ->where('users.id',$user->reporting_user_id)
                    ->first();

                    $sruser = DB::table('users')
                    ->select('users.name','reporting_user_id')
                    ->where('users.id',$ruser->reporting_user_id)
                    ->first();

                    if(!empty($sruser)){ $reporting = $sruser->name; }else{ $reporting = "-"; }
                    return $reporting;
                })
                ->addColumn('cluster_head', function ($row) {
                    $user = DB::table('users')
                    ->select('users.name','reporting_user_id')
                    ->where('users.id',$row->sales_person)
                    ->first();
                    
                    $ruser = DB::table('users')
                    ->select('users.name','reporting_user_id')
                    ->where('users.id',$user->reporting_user_id)
                    ->first();
                    $sruser = DB::table('users')
                    ->select('users.name','reporting_user_id')
                    ->where('users.id',$ruser->reporting_user_id)
                    ->first();
                    $chuser = DB::table('users')
                    ->select('users.name','reporting_user_id')
                    ->where('users.id',$sruser->reporting_user_id)
                    ->first();

                    if(!empty($chuser)){ $reporting = $chuser->name; }else{ $reporting = "-"; }
                    return $reporting;
                })
                ->addColumn("booking_confirm", function ($row) {
                    if (($row->reporting_user_id == auth()->user()->id || auth()->user()->hasRole('Superadmin'))) {
                    return '<div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" value="1" id="flexSwitchCheckDefault2" '.(($row->booking_confirm == 1) ? "checked" : "").' onclick="updateBStatus(this, '.$row->id.');">
                            </div>';
                    }
                    return '';
                })
                ->addColumn("registration_confirm", function ($row) {
                    if (($row->reporting_user_id == auth()->user()->id || auth()->user()->hasRole('Superadmin'))) {
                    return '<div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" value="1" id="flexSwitchCheckDefault" '.(($row->registration_confirm == 1) ? "checked" : "").' onclick="updateStatus(this, '.$row->id.');">
                            </div>';
                    }
                    return '';
                })
                ->addColumn("invoice_raised", function ($row) {
                    if (($row->reporting_user_id == auth()->user()->id || auth()->user()->hasRole('Superadmin'))) {
                    return '<div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" value="1" id="flexSwitchCheckDefault1" '.(($row->invoice_raised == 1) ? "checked" : "").' onclick="updateIStatus(this, '.$row->id.');">
                            </div>';
                    }
                    return '';
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
                    // if (auth()->user()->can('booking-view')) {
                    //     $actions .= '<a class="dropdown-item" target=”_blank”  href="'.url('send_booking_mail/'.$row->id).'"
                    //                     ><i class="bx bx-edit-alt me-1"></i> Send Email</a>';
                    // }

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
                ->rawColumns(['action','registration_confirm','invoice_raised','booking_confirm'])
                ->make(true);
        }
        return view('booking.index');
    }

    public function create()
    {
        $project_name = Project::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $developer_name = Developer::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $user_name = User::select(['id', 'name'])->orderBy('name', 'asc')->get();
       // return view('booking.create');
        return view('booking.create', compact('project_name','developer_name','user_name'));
    }

    public function store(BookingRequest $request)
    {
        //create booking
        $booking = new Booking();
        $booking->project_id = $request->input('project_id');
        $booking->developer_id = $request->input('developer_id');
        $booking->brokerage = $request->input('brokerage');
        $booking->client_name = $request->input('client_name');
        $booking->booking_date = $request->input('booking_date');
        $booking->client_contact = $request->input('client_contact');
        $booking->configuration = $request->input('configuration');
        $booking->flat_no = $request->input('flat_no');
        $booking->wing = $request->input('wing');
        $booking->tower = $request->input('tower');
        $booking->sales_person = $request->input('sales_person');
        $booking->lead_source = $request->input('lead_source');
        $booking->sourcing_manager = $request->input('sourcing_manager');
        $booking->sourcing_contact = $request->input('sourcing_contact');
        $booking->booking_amount = $request->input('booking_amount');
        $booking->agreement_value = $request->input('agreement_value');
        $booking->passback = $request->input('passback');
        $booking->additional_kicker = $request->input('additional_kicker');
        $booking->payment_done = $request->input('payment_done');
        $booking->remark = $request->input('remark');
        $booking->registration_date = $request->input('registration_date');
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
        $booking->project_id = $request->input('project_id');
        $booking->developer_id = $request->input('developer_id');
        $booking->client_name = $request->input('client_name');
        $booking->booking_date = $request->input('booking_date');
        $booking->client_contact = $request->input('client_contact');
        $booking->configuration = $request->input('configuration');
        $booking->flat_no = $request->input('flat_no');
        $booking->wing = $request->input('wing');
        $booking->tower = $request->input('tower');
        $booking->sales_person = $request->input('sales_person');
        $booking->lead_source = $request->input('lead_source');
        $booking->sourcing_manager = $request->input('sourcing_manager');
        $booking->sourcing_contact = $request->input('sourcing_contact');
        $booking->booking_amount = $request->input('booking_amount');
        $booking->agreement_value = $request->input('agreement_value');
        $booking->passback = $request->input('passback');
        $booking->additional_kicker = $request->input('additional_kicker');
        $booking->payment_done = $request->input('payment_done');
        $booking->remark = $request->input('remark');
        $booking->registration_date = $request->input('registration_date');
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

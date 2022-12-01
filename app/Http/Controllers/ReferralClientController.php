<?php

namespace App\Http\Controllers;

use App\Models\ReferralClient;
use App\Models\ClientReference;
use App\Http\Requests\ReferralClientRequest;
use DataTables;
use Illuminate\Http\Request;
use Auth;

class ReferralClientController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:response-view', ['only' => ['index']]);
        $this->middleware('permission:response-edit', ['only' => ['edit','update']]);

    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ReferralClient::where('form_type','=','referrals')->where('created_by','=',Auth::id())->get();
            return DataTables::of($data)
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('mobile', function ($row) {
                    return $row->mobile;
                })
                ->addColumn('status', function ($row) {
                    if($row->status == 0){
                        $r = "Pending";
                    }elseif($row->status == 1){
                        $r = "In Progress";
                    }elseif($row->status == 2){
                        $r = "Completed";
                    }elseif($row->status == 3){
                        $r = "Deleted (Invalid)";
                    }
                    return $r;
                })
                ->addColumn('created_at', function ($row) {
                    return date("d-m-Y", strtotime($row->created_at));
                })
                ->addColumn('updated_at', function ($row) {
                    return ($row->updated_at != "") ? date("d-m-Y", strtotime($row->updated_at)) : '-';
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    // if (auth()->user()->can('response-edit')) {
                    //     $actions .= '<a class="dropdown-item" href="'.route('client_response.edit', $row->id).'"
                    //                     ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    // }
                    if (auth()->user()->can('response-view')) {
                        $actions .= '<a class="dropdown-item" href="'.route('client_response.show', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> View Details</a>';
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
        return view('response.index');
    }


    // public function edit($id)
    // {
    //     $location = Location::find($id);
    //     return view('location.edit', compact('id', 'location'));
    // }

    public function update(ReferralClientRequest $request, $id)
    {
        $rclient = ReferralClient::find($id);
        $rclient->status = $request->input('status');
        $rclient->save();

        return redirect()->back()->with(['success' => 'Status Updated Successfully']);


    }

    public function show($id)
    {
        $rclient = ReferralClient::find($id);
        $creferences = ClientReference::where('referral_client_id','=',$id)->get();

        return view('response.show', compact('id', 'rclient','creferences'));
    }

    public function sresponse(Request $request)
    {
        //echo $sname; exit;
        // $data = ReferralClient::where('form_type','=',$sname)->get();
        // print_r($data); exit;
        if ($request->ajax()) {
            $data = ReferralClient::where('form_type','!=','referrals')->join('users', 'created_by', '=', 'users.id')->where('created_by','=',Auth::id())->orWhere('users.reporting_user_id','=',Auth::id())->get();
            return DataTables::of($data)
                ->addColumn('form_type', function ($row) {
                    if($row->form_type == 'homeloan'){
                        $service = "Home Loan";
                    }elseif($row->form_type == 'collection'){
                        $service = "Collection";
                    }elseif($row->form_type == 'document'){
                        $service = "Documentation";
                    }elseif($row->form_type == 'property'){
                        $service = "Property Management";
                    }
                    return ucwords($service);
                })
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('mobile', function ($row) {
                    return $row->mobile;
                })
                ->addColumn('status', function ($row) {
                    if($row->status == 0){
                        $r = "Pending";
                    }elseif($row->status == 1){
                        $r = "In Progress";
                    }elseif($row->status == 2){
                        $r = "Completed";
                    }elseif($row->status == 3){
                        $r = "Deleted (Invalid)";
                    }
                    return $r;
                })
                ->addColumn('created_at', function ($row) {
                    return date("d-m-Y", strtotime($row->created_at));
                })
                ->addColumn('updated_at', function ($row) {
                    return ($row->updated_at != "") ? date("d-m-Y", strtotime($row->updated_at)) : '-';
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    // if (auth()->user()->can('response-edit')) {
                    //     $actions .= '<a class="dropdown-item" href="'.route('client_response.edit', $row->id).'"
                    //                     ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    // }
                    if (auth()->user()->can('response-view')) {
                        $actions .= '<a class="dropdown-item" href="'.route('client_response.show', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> View Details</a>';
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
        return view('response.sindex');
    }
}

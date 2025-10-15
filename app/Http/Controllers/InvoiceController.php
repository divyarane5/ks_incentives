<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Booking;
use App\Http\Requests\InvoiceRequest;
use DataTables;
use Illuminate\Http\Request;
use DB;

class InvoiceController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:invoice-view', ['only' => ['index']]);
        $this->middleware('permission:invoice-create', ['only' => ['create','store']]);
        $this->middleware('permission:invoice-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:invoice-delete', ['only' => ['destroy']]);

    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Invoice::query();
            return DataTables::of($data)
                ->addColumn('booking_id', function ($row) {
                    $booking = DB::table('bookings')->select('bookings.*','projects.name')->join('projects', 'bookings.project_id','=','projects.id','left')
                    ->where('bookings.id','=',$row->booking_id)->first();
                    return $booking->client_name."-".$booking->name;
                })
                ->addColumn('total_brokerage', function ($row) {
                    $booking = DB::table('bookings')->select('bookings.*')
                    ->where('bookings.id','=',$row->booking_id)->first();
                    $brokerage = DB::table('projects')
                    ->select('projects.brokerage')
                    ->where('projects.id',$booking->project_id)
                    ->first();
                    $t_bookings = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id')
                    ->where('project_ladders.project_id',$booking->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $booking->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $booking->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->get();
                    $project_ladder = DB::table('project_ladders')
                                    ->select('project_ladders.*')
                                    ->where('project_ladders.booking','<=',count($t_bookings))
                                    ->where('project_ladders.project_id',$booking->project_id)
                                    ->whereDate('project_ladders.project_s_date', '<', $booking->booking_date)
                                    ->whereDate('project_ladders.project_e_date', '>', $booking->booking_date)
                                   // ->orderBy('project_ladders.booking','desc')
                                    ->first();
                    $t_agreementv = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id','bookings.agreement_value')
                    ->where('project_ladders.project_id',$booking->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $booking->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $booking->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->sum('bookings.agreement_value');
                    
                    $developer_ladder = DB::table('developer_ladders')
                    ->select('developer_ladders.*')
                    ->where('developer_ladders.aop','>=',$t_agreementv)
                    ->where('developer_ladders.developer_id',$booking->developer_id)
                    ->whereDate('developer_ladders.aop_s_date', '<', $booking->booking_date)
                    ->whereDate('developer_ladders.aop_e_date', '<', $booking->booking_date)
                    ->first();
                    if(!empty($developer_ladder)){
                        //yes
                        return ($developer_ladder->ladder+$project_ladder->ladder + $brokerage->brokerage)." %";
                    }else{
                        if(!empty($project_ladder)){
                            //yes
                            return ($project_ladder->ladder + $brokerage->brokerage)." %";
                        }else{
                            //no
                            return $brokerage->brokerage." %";
                        }
                    }
                    
                       
                })
                ->addColumn('base_revenue', function ($row) {
                    $booking = DB::table('bookings')->select('bookings.*')
                    ->where('bookings.id','=',$row->booking_id)->first();
                    $brokerage = DB::table('projects')
                    ->select('projects.brokerage')
                    ->where('projects.id',$booking->project_id)
                    ->first();
                    $t_bookings = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id')
                    ->where('project_ladders.project_id',$booking->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $booking->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $booking->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->get();
                    $project_ladder = DB::table('project_ladders')
                                    ->select('project_ladders.*')
                                    ->where('project_ladders.booking','<=',count($t_bookings))
                                    ->where('project_ladders.project_id',$booking->project_id)
                                    ->whereDate('project_ladders.project_s_date', '<', $booking->booking_date)
                                    ->whereDate('project_ladders.project_e_date', '>', $booking->booking_date)
                                   // ->orderBy('project_ladders.booking','desc')
                                    ->first();
                    $t_agreementv = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id','bookings.agreement_value')
                    ->where('project_ladders.project_id',$booking->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $booking->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $booking->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->sum('bookings.agreement_value');
                    
                    $developer_ladder = DB::table('developer_ladders')
                    ->select('developer_ladders.*')
                    ->where('developer_ladders.aop','>=',$t_agreementv)
                    ->where('developer_ladders.developer_id',$booking->developer_id)
                    ->whereDate('developer_ladders.aop_s_date', '<', $booking->booking_date)
                    ->whereDate('developer_ladders.aop_e_date', '<', $booking->booking_date)
                    ->first();
                    if(!empty($developer_ladder)){
                        //yes
                        return "Rs ".number_format((($developer_ladder->ladder+$project_ladder->ladder + $brokerage->brokerage)/100)*$booking->agreement_value);
                    }else{
                        if(!empty($project_ladder)){
                            //yes
                            return "Rs ".number_format((($project_ladder->ladder + $brokerage->brokerage)/100)*$booking->agreement_value);
                        }else{
                            //no
                            return "Rs ".number_format(($brokerage->brokerage/100)*$booking->agreement_value);
                        }
                    }
                })
                ->addColumn('invoice_date', function ($row) {
                    return $row->invoice_date;
                })
                ->addColumn('invoice_percent', function ($row) {
                    return $row->invoice_percent." %";
                })
                ->addColumn('invoice_amount', function ($row) {
                    return "Rs. ".$row->invoice_amount." (Rs".number_format((98/100) *$row->invoice_amount).")";
                })
                
                
                ->addColumn('tds', function ($row) {
                    return "2 %";
                })
                ->addColumn('payment_received', function ($row) {
                    if(!empty($row->payment_received)){
                     return "Rs. ".number_format((98/100)*$row->payment_received);
                    }else{
                        return 0;
                    }
                })
                
                ->addColumn('passback_amount', function ($row) {
                    
                    $booking = DB::table('bookings')->select('bookings.*')
                    ->where('bookings.id','=',$row->booking_id)->first();
                    $first_invoice = DB::table('invoices')->select('invoices.*')
                            ->where('booking_id','=',$row->booking_id)
                            ->orderBy('id','asc')
                            ->first();
                            if($first_invoice->invoice_percent == $row->invoice_percent){
                                return $booking->passback;
                            }else{
                                return 0;
                            }
                    
                })
                ->addColumn('actual_revenue', function ($row) {
                    if(!empty($row->payment_received)){
                    $booking = DB::table('bookings')->select('bookings.*')
                    ->where('bookings.id','=',$row->booking_id)->first();
                    $first_invoice = DB::table('invoices')->select('invoices.*')
                            ->where('booking_id','=',$row->booking_id)
                            ->orderBy('id','asc')
                            ->first();
                            if($first_invoice->invoice_percent == $row->invoice_percent){
                                //return $booking->passback;
                                return "Rs. ".number_format(((98/100)*$row->payment_received) - $booking->passback);
                            }else{
                               // return 0;
                               return "Rs. ".number_format((98/100)*$row->payment_received);
                            }
                        }else{
                            return "-";
                        }
                })
                ->addColumn('salary', function ($row) {
                    $booking = DB::table('bookings')->select('bookings.*')
                    ->where('bookings.id','=',$row->booking_id)->first();
                    $user_d = DB::table('users')->select('users.*')
                    ->where('users.id','=',$booking->sales_person)->first();
                    return $user_d->salary;
                })
                ->addColumn('p_invoice_percent', function ($row) {
                    $booking = DB::table('bookings')->select('bookings.*')
                    ->where('bookings.id','=',$row->booking_id)->first();
                    $brokerage = DB::table('projects')
                    ->select('projects.brokerage')
                    ->where('projects.id',$booking->project_id)
                    ->first();
                    $t_bookings = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id')
                    ->where('project_ladders.project_id',$booking->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $booking->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $booking->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->get();
                    $project_ladder = DB::table('project_ladders')
                                    ->select('project_ladders.*')
                                    ->where('project_ladders.booking','<=',count($t_bookings))
                                    ->where('project_ladders.project_id',$booking->project_id)
                                    ->whereDate('project_ladders.project_s_date', '<', $booking->booking_date)
                                    ->whereDate('project_ladders.project_e_date', '>', $booking->booking_date)
                                   // ->orderBy('project_ladders.booking','desc')
                                    ->first();
                    $t_agreementv = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id','bookings.agreement_value')
                    ->where('project_ladders.project_id',$booking->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $booking->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $booking->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->sum('bookings.agreement_value');
                    
                    $developer_ladder = DB::table('developer_ladders')
                    ->select('developer_ladders.*')
                    ->where('developer_ladders.aop','>=',$t_agreementv)
                    ->where('developer_ladders.developer_id',$booking->developer_id)
                    ->whereDate('developer_ladders.aop_s_date', '<', $booking->booking_date)
                    ->whereDate('developer_ladders.aop_e_date', '<', $booking->booking_date)
                    ->first();

                    $latest_invoice = DB::table('invoices')
                                    ->select('invoices.id','invoices.invoice_percent')
                                    ->selectRaw('sum(invoices.invoice_percent)')
                                    ->where('invoices.booking_id','=',$row->booking_id)
                                    ->where('id', '<', $row->id)
                                    ->orderBy('id','DESC')
                                    ->groupBy('invoices.id','invoices.invoice_percent')
                                    ->get();
                    // $latest_invoice = DB::table('invoices')
                    //                 ->select('invoices.id','invoices.invoice_percent')
                    //                 ->selectRaw('sum(invoices.invoice_percent)')
                    //                 ->where('invoices.booking_id','=',$row->booking_id)
                    //                 ->where('id', '<', $row->id)
                    //                 ->orderBy('id','DESC')
                    //                 ->groupBy('invoices.id','invoices.invoice_percent')
                    $latest_invoice = DB::table('invoices')
                                    ->select('invoices.id','invoices.invoice_percent')
                                    ->where('invoices.booking_id','=',$row->booking_id)
                                    ->where('id', '<', $row->id)
                                    ->sum('invoices.invoice_percent');

                    if(!empty($latest_invoice)){
                        $last_invoice_percent = $latest_invoice;
                    }else{
                        $last_invoice_percent = 0;
                    }
                    if(!empty($developer_ladder)){
                        //yes
                        return (($developer_ladder->ladder+$project_ladder->ladder + $brokerage->brokerage)- ($last_invoice_percent + $row->invoice_percent))." %";
                    }else{
                        if(!empty($project_ladder)){
                            //yes
                            return (($project_ladder->ladder + $brokerage->brokerage)- ($last_invoice_percent + $row->invoice_percent))." %";
                        }else{
                            //no
                            return $brokerage->brokerage - ($last_invoice_percent + $row->invoice_percent)." %";
                        }
                    }
                   
                   // return $row->invoice_percent." %";
                })
                ->addColumn('p_invoice_amount', function ($row) {
                    $booking = DB::table('bookings')->select('bookings.*')
                    ->where('bookings.id','=',$row->booking_id)->first();
                    $brokerage = DB::table('projects')
                    ->select('projects.brokerage')
                    ->where('projects.id',$booking->project_id)
                    ->first();
                    $t_bookings = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id')
                    ->where('project_ladders.project_id',$booking->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $booking->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $booking->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->get();
                    $project_ladder = DB::table('project_ladders')
                                    ->select('project_ladders.*')
                                    ->where('project_ladders.booking','<=',count($t_bookings))
                                    ->where('project_ladders.project_id',$booking->project_id)
                                    ->whereDate('project_ladders.project_s_date', '<', $booking->booking_date)
                                    ->whereDate('project_ladders.project_e_date', '>', $booking->booking_date)
                                   // ->orderBy('project_ladders.booking','desc')
                                    ->first();
                    $t_agreementv = DB::table('project_ladders')
                    ->join('bookings', 'bookings.project_id', '=', 'project_ladders.project_id','left')
                    ->select('project_ladders.*','bookings.id','bookings.agreement_value')
                    ->where('project_ladders.project_id',$booking->project_id)
                    ->whereDate('project_ladders.project_s_date', '<', $booking->booking_date)
                    ->whereDate('project_ladders.project_e_date', '>', $booking->booking_date)
                    //->groupBy('project_ladders.*','bookings.id')
                    ->sum('bookings.agreement_value');
                    
                    $developer_ladder = DB::table('developer_ladders')
                    ->select('developer_ladders.*')
                    ->where('developer_ladders.aop','>=',$t_agreementv)
                    ->where('developer_ladders.developer_id',$booking->developer_id)
                    ->whereDate('developer_ladders.aop_s_date', '<', $booking->booking_date)
                    ->whereDate('developer_ladders.aop_e_date', '<', $booking->booking_date)
                    ->first();

                    $latest_invoice = DB::table('invoices')
                                    ->select('invoices.id','invoices.invoice_percent')
                                    ->selectRaw('sum(invoices.invoice_percent)')
                                    ->where('invoices.booking_id','=',$row->booking_id)
                                    ->where('id', '<', $row->id)
                                    ->orderBy('id','DESC')
                                    ->groupBy('invoices.id','invoices.invoice_percent')
                                    ->get();
                    // $latest_invoice = DB::table('invoices')
                    //                 ->select('invoices.id','invoices.invoice_percent')
                    //                 ->selectRaw('sum(invoices.invoice_percent)')
                    //                 ->where('invoices.booking_id','=',$row->booking_id)
                    //                 ->where('id', '<', $row->id)
                    //                 ->orderBy('id','DESC')
                    //                 ->groupBy('invoices.id','invoices.invoice_percent')
                    $latest_invoice = DB::table('invoices')
                                    ->select('invoices.id','invoices.invoice_percent')
                                    ->where('invoices.booking_id','=',$row->booking_id)
                                    ->where('id', '<', $row->id)
                                    ->sum('invoices.invoice_percent');

                    if(!empty($latest_invoice)){
                        $last_invoice_percent = $latest_invoice;
                    }else{
                        $last_invoice_percent = 0;
                    }
                    if(!empty($developer_ladder)){
                        //yes
                        $percent =  (($developer_ladder->ladder+$project_ladder->ladder + $brokerage->brokerage)- ($last_invoice_percent + $row->invoice_percent));
                        return "Rs ".number_format(($percent/100)* $booking->agreement_value);
                    }else{
                        if(!empty($project_ladder)){
                            //yes
                            $percent = (($project_ladder->ladder + $brokerage->brokerage)- ($last_invoice_percent + $row->invoice_percent));
                            return "Rs ".number_format(($percent/100)*$booking->agreement_value);
                        }else{
                            //no
                            $percent =  $brokerage->brokerage - ($last_invoice_percent + $row->invoice_percent);
                            return "Rs ".number_format(($percent/100)* $booking->agreement_value);
                        }
                    }
                })
                ->addColumn('status', function ($row) {
                    return $row->status;
                })
                ->addColumn('created_at', function ($row) {
                    return date("d-m-Y", strtotime($row->created_at));
                })
                ->addColumn('updated_at', function ($row) {
                    return ($row->updated_at != "") ? date("d-m-Y", strtotime($row->updated_at)) : '-';
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->can('invoice-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('invoice.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('invoice-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteInvoice('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('invoice.destroy', $row->id).'" method="POST" class="d-none">
                                        '.csrf_field().'
                                        '.method_field('delete').'
                                    </form>';
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
                ->rawColumns(['action','total_brokerage'])
                ->make(true);
        }
        return view('invoice.index');
    }

    public function create()
    {
        $bookings = Booking::select(['bookings.id', 'bookings.client_name','projects.name','bookings.agreement_value'])
        ->join('projects', 'bookings.project_id','=','projects.id','left')
        ->groupBy('bookings.id', 'bookings.client_name','projects.name','agreement_value')
        ->orderBy('name', 'asc')->get();
       // print_r($bookings); exit;
        //return view('invoice.create');
        return view('invoice.create', compact('bookings'));
    }

    public function store(InvoiceRequest $request)
    {
        //create invoice
        $invoice = new Invoice();
        $invoice->booking_id = $request->input('booking_id');
        $invoice->invoice_date = $request->input('invoice_date');
        $invoice->invoice_percent = $request->input('invoice_percent');
        $invoice->invoice_amount = $request->input('invoice_amount');
        $invoice->payment_received = $request->input('payment_received');
        $invoice->save();

        return redirect()->route('invoice.index')->with('success', 'Invoice Added Successfully');
    }

    public function edit($id)
    {
        $invoice = Invoice::find($id);
        return view('invoice.edit', compact('id', 'invoice'));
    }

    public function update(InvoiceRequest $request, $id)
    {
        $invoice = Invoice::find($id);
        $invoice->name = $request->input('name');
        $invoice->brokerage = $request->input('brokerage');
        $invoice->save();

        return redirect()->route('invoice.index')->with('success', 'Invoice Updated Successfully');
    }

    public function destroy($id)
    {
        Invoice::where('id', $id)->delete();
        return redirect()->route('invoice.index')->with('success', 'Invoice Deleted Successfully');
    }
}

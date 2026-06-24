<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingBrokeragePayment;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Project;
use App\Models\Developer;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BrokeragePaymentsExport;

class BookingBrokeragePaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(
            'permission:payment-view',
            ['only' => ['index','datatable']]
        );

        $this->middleware(
            'permission:payment-create',
            ['only' => ['create','store']]
        );

        $this->middleware(
            'permission:payment-edit',
            ['only' => ['edit','update']]
        );

        $this->middleware(
            'permission:payment-delete',
            ['only' => ['destroy']]
        );
    }
    public function export(Request $request)
    {
        return Excel::download(
            new BrokeragePaymentsExport($request->all()),
            'brokerage-payments.xlsx'
        );
    }
    public function index()
    {
        return view('booking_brokerage_payments.index');
    }
    public function datatable(Request $request)
    {
        $query = BookingBrokeragePayment::with([
            'booking.project',
            'booking.developer'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        if ($request->project_id) {

            $query->whereHas('booking', function ($q) use ($request) {

                $q->where('project_id', $request->project_id);

            });
        }

        if ($request->developer_id) {

            $query->whereHas('booking', function ($q) use ($request) {

                $q->where('developer_id', $request->developer_id);

            });
        }

        if ($request->status) {

            $query->where('status', $request->status);
        }

        if ($request->date_from) {

            $query->whereDate(
                'invoice_date',
                '>=',
                $request->date_from
            );
        }

        if ($request->date_to) {

            $query->whereDate(
                'invoice_date',
                '<=',
                $request->date_to
            );
        }

        return datatables()->of($query)

            ->addColumn('booking_id', function($row){
                return '<a href="'.url('booking/'.$row->booking->id).'"
                    target="_blank">
                    #'.$row->booking->id.'
                </a>';
            })

            ->addColumn('client_name', function($row){
                return $row->booking->client_name ?? '-';
            })

            ->addColumn('project_name', function($row){
                return optional($row->booking->project)->name ?? '-';
            })
            ->addColumn('developer_name', function($row){

                return optional(
                    $row->booking->developer
                )->name ?? '-';

            })
            ->addColumn('invoice_date_format', function($row){

                return $row->invoice_date
                    ? date('d-m-Y',strtotime($row->invoice_date))
                    : '-';
            })

            ->addColumn('invoice_amount_format', function($row){
                return number_format($row->invoice_amount,2);
            })

            ->addColumn('received_amount_format', function($row){
                return number_format($row->bank_received_amount,2);
            })
            ->addColumn('tds_amount_format', function($row){

                return number_format(
                    $row->tds_amount,
                    2
                );
            })
            ->addColumn('outstanding_amount_format', function($row){

                $outstanding =
                    $row->invoice_amount
                    -
                    $row->bank_received_amount
                    -
                    $row->tds_amount;

                return number_format(
                    max(0,$outstanding),
                    2
                );

            })
            ->addColumn('remarks_text', function($row){

                return $row->remarks ?? '-';

            })
            ->addColumn('status_badge', function($row){

                if($row->status == 'received'){
                    return '<span class="badge bg-success">Received</span>';
                }

                if($row->status == 'invoice_raised'){
                    return '<span class="badge bg-warning">Invoice Raised</span>';
                }

                return '<span class="badge bg-secondary">Pending</span>';
            })
            ->addColumn('invoice_age', function ($row) {

                if ($row->status == 'received') {

                    return '<span class="badge bg-success">
                                Paid
                            </span>';
                }

                if (!$row->invoice_date) {

                    return '<span class="badge bg-secondary">
                                No Invoice Date
                            </span>';
                }

                $days = \Carbon\Carbon::parse($row->invoice_date)
                    ->diffInDays(now());

                if ($days <= 30) {

                    return '<span class="badge bg-info">
                                '.$days.' Days
                            </span>';
                }

                if ($days <= 60) {

                    return '<span class="badge bg-warning">
                                '.$days.' Days
                            </span>';
                }

                if ($days <= 90) {

                    return '<span class="badge bg-orange">
                                '.$days.' Days
                            </span>';
                }

                return '<span class="badge bg-danger">
                            '.$days.' Days
                        </span>';
            })
            ->addColumn('invoice_file_html', function($row){

                if(!$row->invoice_file){
                    return '-';
                }

                return '<a href="'.asset('storage/'.$row->invoice_file).'"
                            target="_blank"
                            class="btn btn-sm btn-info">
                            View
                        </a>';
            })
            ->addColumn('action', function($row){

                return '
                <button
                    class="btn btn-sm btn-primary editInvoiceBtn"
                    data-id="'.$row->id.'">
                    Edit
                </button>';
            })

            ->rawColumns([
                'booking_id',
                'status_badge',
                'invoice_age',
                'invoice_file_html',
                'action'
            ])

            ->make(true);
    }
    public function projects()
    {
        return \App\Models\Project::whereHas('bookings.brokeragePayments')
            ->select('id','name')
            ->orderBy('name')
            ->get();
    }

    public function developers()
    {
        return \App\Models\Developer::whereHas('bookings.brokeragePayments')
            ->select('id','name')
            ->orderBy('name')
            ->get();
    }
    public function create()
    {
        $bookings = Booking::with('project')
            ->where('booking_confirm', 'approved')
            ->orderBy('id','desc')
            ->get();

        return view(
            'booking_brokerage_payments.create',
            compact('bookings')
        );
    }

    public function edit($id)
    {
        $payment = BookingBrokeragePayment::with('booking.project')
            ->findOrFail($id);

        return view(
            'booking_brokerage_payments.edit',
            compact('payment')
        );
    }
    public function destroy($id)
    {
        $payment = BookingBrokeragePayment::findOrFail($id);

        $bookingId = $payment->booking_id;

        $payment->delete();

        $this->updateBookingPaymentSummary($bookingId);

        return response()->json([
            'success' => true
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'invoice_percent' => 'nullable|numeric|min:0',
            'invoice_amount' => 'nullable|numeric|min:0',
            'bank_received_amount' => 'nullable|numeric|min:0',
            'tds_amount' => 'nullable|numeric|min:0',
            'invoice_date' => 'nullable|date',
            'bank_received_date' => 'nullable|date',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls|max:5120',
            'invoice_number' => 'nullable|string|max:100',
            'invoice_type' => 'required',
            'developer_billing_entity_id' => 'nullable|integer',
            'company_bank_account_id' => 'nullable|integer',

            'cgst_percent' => 'nullable|numeric',
            'cgst_amount' => 'nullable|numeric',

            'sgst_percent' => 'nullable|numeric',
            'sgst_amount' => 'nullable|numeric',

            'total_gst_amount' => 'nullable|numeric',

            'credit_note_date' => 'nullable|date',
            'credit_note_reason' => 'nullable|string',
            'credit_note_number' => 'nullable|string|max:100',
        ]);
        if(
            empty($request->invoice_percent) &&
            empty($request->invoice_amount) &&
            empty($request->bank_received_amount) &&
            empty($request->tds_amount)
        ){
            return back()->with('error','Cannot save empty payment row.');
        }
        $totalInvoicePercent = BookingBrokeragePayment::where('booking_id',$request->booking_id)
                ->sum('invoice_percent');

        $booking = Booking::findOrFail($request->booking_id);

        $newPercent = $request->invoice_percent ?? 0;

        if(($totalInvoicePercent + $newPercent) > $booking->total_brokerage_percent){
            return back()->with('error','Invoice percent exceeds total brokerage limit');
        }
        $payment = new BookingBrokeragePayment();

        $payment->booking_id = $request->booking_id;
        $payment->invoice_number = $request->invoice_number;
        $payment->invoice_type = $request->invoice_type;

        $payment->developer_billing_entity_id =
            $request->developer_billing_entity_id;

        $payment->company_bank_account_id =
            $request->company_bank_account_id;

        $payment->cgst_percent = $request->cgst_percent ?? 0;
        $payment->cgst_amount = $request->cgst_amount ?? 0;

        $payment->sgst_percent = $request->sgst_percent ?? 0;
        $payment->sgst_amount = $request->sgst_amount ?? 0;

        $payment->total_gst_amount =
            $request->total_gst_amount ?? 0;
        $payment->credit_note_number =
            $request->credit_note_number;
        $payment->credit_note_date =
            $request->credit_note_date;

        $payment->credit_note_reason =
            $request->credit_note_reason;
        $payment->invoice_percent = $request->invoice_percent ?? 0;
        $payment->invoice_amount = $request->invoice_amount ?? 0;
        $payment->invoice_date = $request->invoice_date;

        $payment->bank_received_amount = $request->bank_received_amount ?? 0;
        $payment->bank_received_date = $request->bank_received_date;
        $payment->tds_amount = $request->tds_amount ?? 0;
        $payment->remarks = $request->remarks;

        if ($request->bank_received_amount > 0) {
            $payment->status = 'received';
        } else {
            $payment->status = 'invoice_raised';
        }
        if($request->hasFile('invoice_file')){

        $file = $request->file('invoice_file')
                        ->store('invoice-files','public');

        $payment->invoice_file = $file;
    }

        $payment->save();

        $this->updateBookingPaymentSummary($payment->booking_id);

        return back()->with('success','Payment Added');
    }


    public function history($id)
    {
        $payments = BookingBrokeragePayment::where('booking_id',$id)
                    ->orderBy('id','desc')
                    ->get();

        return response()->json($payments);
    }


    public function update(Request $request,$id)
    {
        $request->validate([
            'invoice_percent' => 'nullable|numeric|min:0',
            'invoice_amount' => 'nullable|numeric|min:0',
            'bank_received_amount' => 'nullable|numeric|min:0',
            'tds_amount' => 'nullable|numeric|min:0',
            'invoice_date' => 'nullable|date',
            'bank_received_date' => 'nullable|date',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls|max:5120',
            'invoice_number' => 'nullable|string|max:100',
            'invoice_type' => 'required',
            'developer_billing_entity_id' => 'nullable|integer',
            'company_bank_account_id' => 'nullable|integer',

            'cgst_percent' => 'nullable|numeric',
            'cgst_amount' => 'nullable|numeric',

            'sgst_percent' => 'nullable|numeric',
            'sgst_amount' => 'nullable|numeric',

            'total_gst_amount' => 'nullable|numeric',

            'credit_note_date' => 'nullable|date',
            'credit_note_reason' => 'nullable|string',
            'credit_note_number' => 'nullable|string|max:100',
        ]);
        if(
            empty($request->invoice_percent) &&
            empty($request->invoice_amount) &&
            empty($request->bank_received_amount) &&
            empty($request->tds_amount) &&
            empty($request->invoice_date) &&
            empty($request->bank_received_date) &&
            empty($request->invoice_number) &&
            empty($request->remarks) &&
            !$request->hasFile('invoice_file')
        ){
            return back()->with('error','Cannot update empty payment row.');
        }
        $payment = BookingBrokeragePayment::findOrFail($id);
        $booking = Booking::findOrFail($payment->booking_id);

        $usedPercent = BookingBrokeragePayment::where(
                'booking_id',
                $payment->booking_id
            )
            ->where('id', '!=', $payment->id)
            ->sum('invoice_percent');

        $newPercent = $request->invoice_percent ?? 0;

        if(($usedPercent + $newPercent) > $booking->total_brokerage_percent){

            return back()->with(
                'error',
                'Invoice percent exceeds total brokerage limit'
            );
        }
        $payment->invoice_percent = $request->invoice_percent;
        $payment->invoice_amount = $request->invoice_amount;
        $payment->invoice_date = $request->invoice_date;
        $payment->invoice_number = $request->invoice_number;

        $payment->invoice_type = $request->invoice_type;

        $payment->developer_billing_entity_id =
            $request->developer_billing_entity_id;

        $payment->company_bank_account_id =
            $request->company_bank_account_id;

        /*
        |--------------------------------------------------------------------------
        | GST
        |--------------------------------------------------------------------------
        */

        $payment->cgst_percent = $request->cgst_percent ?? 9;
        $payment->sgst_percent = $request->sgst_percent ?? 9;

        $payment->cgst_amount = $request->cgst_amount ?? 0;
        $payment->sgst_amount = $request->sgst_amount ?? 0;

        $payment->total_gst_amount =
            $request->total_gst_amount ?? 0;

        /*
        |--------------------------------------------------------------------------
        | Credit Note
        |--------------------------------------------------------------------------
        */
        $payment->credit_note_number =
            $request->credit_note_number;
        $payment->credit_note_date =
            $request->credit_note_date;

        $payment->credit_note_reason =
            $request->credit_note_reason;
        $payment->bank_received_amount = $request->bank_received_amount;
        $payment->bank_received_date = $request->bank_received_date;

        $payment->tds_amount = $request->tds_amount;

        $payment->remarks = $request->remarks;

        if (($request->bank_received_amount ?? 0) > 0) {

            $payment->status = 'received';

        } elseif (($request->invoice_amount ?? 0) > 0) {

            $payment->status = 'invoice_raised';

        } else {

            $payment->status = 'pending';
        }

        if($request->hasFile('invoice_file')){

            $file = $request->file('invoice_file')
                            ->store('invoice-files','public');

            $payment->invoice_file = $file;
        }

        $payment->save();

        // recalculate booking summary
        $this->updateBookingPaymentSummary($payment->booking_id);

        return back()->with('success','Payment updated successfully');
    }


     private function updateBookingPaymentSummary($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $totalInvoicePercent = BookingBrokeragePayment::where('booking_id',$bookingId)
            ->sum('invoice_percent');

        $totalInvoiceAmount = BookingBrokeragePayment::where('booking_id',$bookingId)
            ->sum('invoice_amount');
        $totalTdsAmount = BookingBrokeragePayment::where('booking_id',$bookingId)
        ->sum('tds_amount');
        $totalReceivedAmount = BookingBrokeragePayment::where('booking_id',$bookingId)
            ->sum('bank_received_amount');
        $totalSettledAmount = $totalReceivedAmount + $totalTdsAmount;
        $totalBrokeragePercent = $booking->total_brokerage_percent;
        $totalBrokerageAmount = $booking->final_revenue;

        /*
        |---------------------------------------
        | Calculate Pending Brokerage
        |---------------------------------------
        */

        $pendingPercent = max(0, $totalBrokeragePercent - $totalInvoicePercent);

        $pendingAmount = max(0, $totalBrokerageAmount - $totalSettledAmount);

        /*
        |--------------------------------------- 
        | Determine Payment Status
        |---------------------------------------
        */

        if ($totalSettledAmount <= 0) {

            $status = 'pending';

        } elseif ($totalSettledAmount < $totalBrokerageAmount) {

            $status = 'partial';

        } else {

            $status = 'completed';
        }
        /*
        |---------------------------------------
        | Update Booking
        |---------------------------------------
        */

        $booking->invoice_raised = $totalInvoicePercent > 0 ? 1 : 0;
        $booking->total_invoice_percent = round($totalInvoicePercent,2);
        $booking->total_invoice_amount = round($totalInvoiceAmount,2);
        $booking->total_received_amount = round($totalReceivedAmount,2);
        $booking->pending_brokerage_percent = round($pendingPercent,2);
        $booking->pending_brokerage_amount = round($pendingAmount,2);
        $booking->payment_status = $status;

        $booking->save();
    }
    public function summary(Request $request)
    {
        $query = BookingBrokeragePayment::query();

        if($request->project_id){
            $query->whereHas('booking', function($q) use ($request){
                $q->where('project_id',$request->project_id);
            });
        }

        if($request->developer_id){
            $query->whereHas('booking', function($q) use ($request){
                $q->where('developer_id',$request->developer_id);
            });
        }

        if($request->status){
            $query->where('status',$request->status);
        }

        if($request->date_from){
            $query->whereDate('invoice_date','>=',$request->date_from);
        }

        if($request->date_to){
            $query->whereDate('invoice_date','<=',$request->date_to);
        }

        $invoice = $query->sum('invoice_amount');

        $received = $query->sum('bank_received_amount');

        $tds = $query->sum('tds_amount');
        $collectionEfficiency = 0;

        if($invoice > 0){

            $collectionEfficiency =
                min(
                    100,
                    (($received + $tds) / $invoice) * 100
                );
        }
        $pending =
            max(
                0,
                $invoice - ($received + $tds)
            );
        $excessCollection =
            max(
                0,
                ($received + $tds) - $invoice
            );
        $outstanding = $pending;
        return response()->json([

            'invoice' => $invoice,
            'received' => $received,
            'tds' => $tds,
            'pending' => $pending,
            'collection_efficiency' => round($collectionEfficiency,2),

            'excess_collection' => $excessCollection,

            'outstanding' => $outstanding

        ]);
    }
    public function agingReport()
    {
        $today = now();

        $payments = BookingBrokeragePayment::where(
            'status',
            '!=',
            'received'
        )->get();

        $aging30 = 0;
        $aging60 = 0;
        $aging90 = 0;
        $aging90plus = 0;

        foreach($payments as $payment){

            if(!$payment->invoice_date){
                continue;
            }

            $days = $today->diffInDays(
                $payment->invoice_date
            );

            $amount =
                $payment->invoice_amount -
                $payment->bank_received_amount -
                $payment->tds_amount;

            if($days <= 30){

                $aging30 += $amount;

            } elseif($days <= 60){

                $aging60 += $amount;

            } elseif($days <= 90){

                $aging90 += $amount;

            } else {

                $aging90plus += $amount;
            }
        }

        return response()->json([
            'aging30' => $aging30,
            'aging60' => $aging60,
            'aging90' => $aging90,
            'aging90plus' => $aging90plus,
        ]);
    }
}
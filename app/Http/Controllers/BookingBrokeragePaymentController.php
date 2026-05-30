<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingBrokeragePayment;
use Yajra\DataTables\Facades\DataTables;

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

        return datatables()->of($query)

            ->addColumn('booking_id', function($row){
                return $row->booking->id ?? '-';
            })

            ->addColumn('client_name', function($row){
                return $row->booking->client_name ?? '-';
            })

            ->addColumn('project_name', function($row){
                return optional($row->booking->project)->name ?? '-';
            })

            ->addColumn('invoice_amount_format', function($row){
                return number_format($row->invoice_amount,2);
            })

            ->addColumn('received_amount_format', function($row){
                return number_format($row->bank_received_amount,2);
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

            ->addColumn('action', function($row){

                return '
                <button
                    class="btn btn-sm btn-primary editInvoiceBtn"
                    data-id="'.$row->id.'">
                    Edit
                </button>';
            })

            ->rawColumns(['status_badge','action'])

            ->make(true);
    }
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'invoice_percent' => 'nullable|numeric',
            'invoice_amount' => 'nullable|numeric',
            'invoice_date' => 'nullable|date',
            'bank_received_date' => 'nullable|date',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls|max:5120',
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
        if(
            empty($request->invoice_percent) &&
            empty($request->invoice_amount) &&
            empty($request->bank_received_amount) &&
            empty($request->tds_amount) &&
            empty($request->invoice_date) &&
            empty($request->bank_received_date) &&
            empty($request->remarks) &&
            !$request->hasFile('invoice_file')
        ){
            return back()->with('error','Cannot update empty payment row.');
        }
        $payment = BookingBrokeragePayment::findOrFail($id);

        $payment->invoice_percent = $request->invoice_percent;
        $payment->invoice_amount = $request->invoice_amount;
        $payment->invoice_date = $request->invoice_date;

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

}
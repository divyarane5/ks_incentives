<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingBrokeragePayment;

class BookingPaymentSummaryService
{
    public function recalculate($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $payments = BookingBrokeragePayment::where(
            'booking_id',
            $bookingId
        )->get();

        $totalInvoicePercent =
            $payments->sum('invoice_percent');

        $totalInvoiceAmount =
            $payments->sum('invoice_amount');

        /*
        IMPORTANT:
        actual collection =
        bank received + tds
        */

        $totalReceived =
            $payments->sum('bank_received_amount')
            +
            $payments->sum('tds_amount');

        $pendingAmount =
            $booking->final_revenue - $totalReceived;

        $pendingPercent = 0;

        if($booking->final_revenue > 0){

            $pendingPercent =
                ($pendingAmount / $booking->final_revenue) * 100;
        }

        /*
        PAYMENT STATUS
        */

        if($totalReceived <= 0){

            $status = 'pending';

        }
        elseif($pendingAmount <= 1){

            $status = 'completed';

        }
        else{

            $status = 'partial';
        }

        $booking->update([

            'total_invoice_percent' =>
                round($totalInvoicePercent,2),

            'total_invoice_amount' =>
                round($totalInvoiceAmount,2),

            'total_received_amount' =>
                round($totalReceived,2),

            'pending_brokerage_amount' =>
                round($pendingAmount,2),

            'pending_brokerage_percent' =>
                round($pendingPercent,2),

            'payment_status' => $status

        ]);
    }
}
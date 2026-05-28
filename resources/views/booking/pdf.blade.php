
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>
        Booking #{{ $booking->id }}
    </title>

    <style>

        body{
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2{
            margin-bottom: 5px;
        }

        .section{
            margin-top: 25px;
        }

        .section-title{
            background: #2f3e4d;
            color: white;
            padding: 8px 10px;
            font-size: 14px;
            font-weight: bold;
        }

        table{
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th{
            background: #f3f3f3;
            text-align: left;
            width: 35%;
        }

        th, td{
            border: 1px solid #ddd;
            padding: 8px;
        }

        .text-center{
            text-align: center;
        }

        .badge{
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
            font-size: 11px;
        }

        .success{
            background: green;
        }

        .warning{
            background: orange;
        }

        .danger{
            background: red;
        }

        .secondary{
            background: gray;
        }

        .totals{
            background: #2f3e4d;
            color: white;
            font-weight: bold;
        }

    </style>

</head>

<body>

    <h2>
        Booking #{{ $booking->id }}
    </h2>

    <p>
        Generated On:
        {{ now() }}
    </p>

    {{-- STATUS --}}
    <div class="section">

        <div class="section-title">
            Booking Status
        </div>

        <table>

            <tr>

                <th>
                    Booking Status
                </th>

                <td>

                    @if($booking->booking_confirm == 'approved')

                        Approved

                    @elseif($booking->booking_confirm == 'cancelled')

                        Cancelled

                    @else

                        Pending

                    @endif

                </td>

            </tr>

            <tr>

                <th>
                    Payment Status
                </th>

                <td>
                    {{ ucfirst($booking->payment_status) }}
                </td>

            </tr>

        </table>

    </div>


    {{-- PAGE INFO --}}
    <div class="section">

        <div class="section-title">
            Booking Information
        </div>

        <table>

            <tr>
                <th>Booking Date</th>
                <td>{{ $booking->booking_date ?? '-' }}</td>
            </tr>

            <tr>
                <th>Registration Date</th>
                <td>{{ $booking->registration_date ?? '-' }}</td>
            </tr>

            <tr>
                <th>Lead Source</th>
                <td>{{ $booking->lead_source ?? '-' }}</td>
            </tr>

            <tr>
                <th>Payment Follow Up</th>
                <td>{{ $booking->payment_followup_date ?? '-' }}</td>
            </tr>

            <tr>
                <th>Invoice Raised</th>
                <td>
                    {{ $booking->invoice_raised ? 'Yes' : 'No' }}
                </td>
            </tr>

            <tr>
                <th>Created By</th>
                <td>{{ $booking->created_by ?? '-' }}</td>
            </tr>

        </table>

    </div>


    {{-- FINANCIAL SUMMARY --}}
    <div class="section">

        <div class="section-title">
            Financial Summary
        </div>

        <table>

            <tr>
                <th>Agreement Value</th>
                <td>₹ {{ number_format($booking->agreement_value ?? 0,2) }}</td>
            </tr>

            <tr>
                <th>Base Brokerage %</th>
                <td>{{ number_format($booking->base_brokerage_percent ?? 0,2) }}%</td>
            </tr>

            <tr>
                <th>Site Ladder %</th>
                <td>{{ number_format($booking->site_ladder_percent ?? 0,2) }}%</td>
            </tr>

            <tr>
                <th>AOP Ladder %</th>
                <td>{{ number_format($booking->aop_ladder_percent ?? 0,2) }}%</td>
            </tr>

            <tr>
                <th>Total Brokerage %</th>
                <td>{{ number_format($booking->total_brokerage_percent ?? 0,2) }}%</td>
            </tr>

            <tr>
                <th>Revenue</th>
                <td>₹ {{ number_format($booking->current_effective_amount ?? 0,2) }}</td>
            </tr>

            <tr>
                <th>Passback</th>
                <td>₹ {{ number_format($booking->passback ?? 0,2) }}</td>
            </tr>

            <tr>
                <th>Additional Kicker</th>
                <td>₹ {{ number_format($booking->additional_kicker ?? 0,2) }}</td>
            </tr>

            <tr>
                <th>Final Revenue</th>
                <td>₹ {{ number_format($booking->final_revenue ?? 0,2) }}</td>
            </tr>

            <tr>
                <th>Amount Receivable</th>
                <td>₹ {{ number_format($booking->amount_receivable ?? 0,2) }}</td>
            </tr>

            <tr>
                <th>TDS Amount</th>
                <td>₹ {{ number_format($booking->tds_amount ?? 0,2) }}</td>
            </tr>

            <tr>
                <th>Total Invoice %</th>
                <td>{{ number_format($booking->total_invoice_percent ?? 0,2) }}%</td>
            </tr>

            <tr>
                <th>Total Invoice Amount</th>
                <td>₹ {{ number_format($booking->total_invoice_amount ?? 0,2) }}</td>
            </tr>

            <tr>
                <th>Total Received Amount</th>
                <td>₹ {{ number_format($booking->total_received_amount ?? 0,2) }}</td>
            </tr>

            <tr>
                <th>Pending Brokerage %</th>
                <td>{{ number_format($booking->pending_brokerage_percent ?? 0,2) }}%</td>
            </tr>

            <tr>
                <th>Pending Brokerage Amount</th>
                <td>₹ {{ number_format($booking->pending_brokerage_amount ?? 0,2) }}</td>
            </tr>

        </table>

    </div>


    {{-- CLIENT DETAILS --}}
    <div class="section">

        <div class="section-title">
            Client Details
        </div>

        <table>

            <tr>
                <th>Client Name</th>
                <td>{{ $booking->client_name ?? '-' }}</td>
            </tr>

            <tr>
                <th>Contact</th>
                <td>{{ $booking->client_contact ?? '-' }}</td>
            </tr>

            <tr>
                <th>Lead Source</th>
                <td>{{ $booking->lead_source ?? '-' }}</td>
            </tr>

            <tr>
                <th>Booking Date</th>
                <td>{{ $booking->booking_date ?? '-' }}</td>
            </tr>

            <tr>
                <th>Remarks</th>
                <td>{{ $booking->remark ?? '-' }}</td>
            </tr>

        </table>

    </div>


    {{-- PROJECT DETAILS --}}
    <div class="section">

        <div class="section-title">
            Project Details
        </div>

        <table>

            <tr>
                <th>Project</th>
                <td>{{ optional($booking->project)->name ?? '-' }}</td>
            </tr>

            <tr>
                <th>Developer</th>
                <td>{{ optional($booking->developer)->name ?? '-' }}</td>
            </tr>

            <tr>
                <th>Configuration</th>
                <td>{{ $booking->configuration ?? '-' }}</td>
            </tr>

            <tr>
                <th>Wing</th>
                <td>{{ $booking->wing ?? '-' }}</td>
            </tr>

            <tr>
                <th>Tower</th>
                <td>{{ $booking->tower ?? '-' }}</td>
            </tr>

            <tr>
                <th>Flat No</th>
                <td>{{ $booking->flat_no ?? '-' }}</td>
            </tr>

            <tr>
                <th>Booking Amount</th>
                <td>₹ {{ number_format($booking->booking_amount ?? 0,2) }}</td>
            </tr>

        </table>

    </div>


    {{-- TEAM --}}
    <div class="section">

        <div class="section-title">
            Team Hierarchy
        </div>

        @php

            $salesManager = optional($booking->user);

            $tl = optional($salesManager->reportingManager);

            $srTl = optional($tl->reportingManager);

            $clusterHead = optional($srTl->reportingManager);

        @endphp

        <table>

            <tr>
                <th>Sales Manager</th>
                <td>{{ $salesManager->name ?? '-' }}</td>
            </tr>

            <tr>
                <th>Team Leader</th>
                <td>{{ $tl->name ?? '-' }}</td>
            </tr>

            <tr>
                <th>Senior TL</th>
                <td>{{ $srTl->name ?? '-' }}</td>
            </tr>

            <tr>
                <th>Cluster Head</th>
                <td>{{ $clusterHead->name ?? '-' }}</td>
            </tr>

        </table>

    </div>


    {{-- PAYMENT HISTORY --}}
    <div class="section">

        <div class="section-title">
            Brokerage Invoice History
        </div>

        @php

            $totalInvoice = $booking->brokeragePayments->sum('invoice_amount');

            $totalReceived = $booking->brokeragePayments->sum('bank_received_amount');

            $totalTds = $booking->brokeragePayments->sum('tds_amount');

        @endphp

        <table>

            <thead>

                <tr>

                    <th>Date</th>
                    <th>Invoice %</th>
                    <th>Invoice Amount</th>
                    <th>TDS</th>
                    <th>Received</th>
                    <th>Status</th>
                    <th>Remarks</th>

                </tr>

            </thead>

            <tbody>

                @forelse($booking->brokeragePayments as $payment)

                    <tr>

                        <td>{{ $payment->invoice_date ?? '-' }}</td>

                        <td>
                            {{ number_format($payment->invoice_percent ?? 0,2) }}%
                        </td>

                        <td>
                            ₹ {{ number_format($payment->invoice_amount ?? 0,2) }}
                        </td>

                        <td>
                            ₹ {{ number_format($payment->tds_amount ?? 0,2) }}
                        </td>

                        <td>
                            ₹ {{ number_format($payment->bank_received_amount ?? 0,2) }}
                        </td>

                        <td>
                            {{ ucfirst($payment->status) }}
                        </td>

                        <td>
                            {{ $payment->remarks ?? '-' }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="text-center">
                            No invoice history found
                        </td>

                    </tr>

                @endforelse

                <tr class="totals">

                    <td colspan="2">
                        Totals
                    </td>

                    <td>
                        ₹ {{ number_format($totalInvoice,2) }}
                    </td>

                    <td>
                        ₹ {{ number_format($totalTds,2) }}
                    </td>

                    <td>
                        ₹ {{ number_format($totalReceived,2) }}
                    </td>

                    <td colspan="2">
                        -
                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</body>
</html>

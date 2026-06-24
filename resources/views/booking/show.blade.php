@extends('layouts.app')

@section('content')

<style>

.table th{
    width: 35%;
    background: #f7f7f7;
    font-weight: 600;
}

.card{
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}

.badge{
    font-size: 12px;
    padding: 7px 10px;
}

.summary-card{
    transition: 0.2s ease;
}

.summary-card:hover{
    transform: translateY(-2px);
}

.info-title{
    font-size: 12px;
    color: #8592a3;
    margin-bottom: 5px;
}

.info-value{
    font-size: 15px;
    font-weight: 600;
}

.section-title{
    font-size: 16px;
    font-weight: 700;
}

.table td{
    vertical-align: middle;
}

</style>

<div class="container-xxl flex-grow-1 container-p-y">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-bold mb-1">
                Booking #{{ $booking->id }}
            </h4>

            <div class="mt-1">

                {{-- BOOKING STATUS --}}
                @if($booking->booking_confirm == 'approved')
                    <span class="badge bg-success">Approved</span>
                @elseif($booking->booking_confirm == 'cancelled')
                    <span class="badge bg-danger">Cancelled</span>
                @else
                    <span class="badge bg-warning">Pending</span>
                @endif

                {{-- PAYMENT STATUS --}}
                @if($booking->payment_status == 'completed')
                    <span class="badge bg-success">Completed</span>
                @elseif($booking->payment_status == 'partial')
                    <span class="badge bg-warning">Partial</span>
                @else
                    <span class="badge bg-danger">Pending</span>
                @endif

            </div>

        </div>

        <div>

            @if(!isset($pdf))

                <a href="#"
                class="btn btn-success">
                    <i class="bx bx-money"></i>
                    Add Invoice
                </a>

                <a href="{{ route('booking.pdf', $booking->id) }}"
                class="btn btn-primary">
                    <i class="bx bx-download"></i>
                    Download PDF
                </a>

                <a href="{{ route('booking.index') }}"
                class="btn btn-secondary">
                    Back
                </a>

                <a href="{{ route('booking.edit',$booking->id) }}"
                class="btn btn-primary">
                    Edit Booking
                </a>

            @endif

        </div>

    </div>

    {{-- ================= PAGE INFO BAR ================= --}}
    <div class="card mb-4 border-0 bg-label-secondary">

        <div class="card-body py-3">

            <div class="row align-items-center">

                <div class="col-md-2">

                    <div class="info-title">
                        Booking Date
                    </div>

                    <div class="info-value">
                        {{ $booking->booking_date ?? '-' }}
                    </div>

                </div>

                <div class="col-md-2">

                    <div class="info-title">
                        Registration Date
                    </div>

                    <div class="info-value">
                        {{ $booking->registration_date ?? '-' }}
                    </div>

                </div>

                <div class="col-md-2">

                    <div class="info-title">
                        Lead Source
                    </div>

                    <div class="info-value">
                        {{ $booking->lead_source ?? '-' }}
                    </div>

                </div>

                <div class="col-md-2">

                    <div class="info-title">
                        Payment Follow Up
                    </div>

                    <div class="info-value text-danger">
                        {{ $booking->payment_followup_date ?? '-' }}
                    </div>

                </div>

                <div class="col-md-2">

                    <div class="info-title">
                        Invoice Raised
                    </div>

                    <div class="info-value">

                        @if($booking->invoice_raised)
                            Yes
                        @else
                            No
                        @endif

                    </div>

                </div>

                <div class="col-md-2">

                    <div class="info-title">
                        Created By
                    </div>

                    <div class="info-value">
                        {{ $booking->created_by ?? '-' }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- SUMMARY CARDS --}}

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Financial Summary</h5>
        </div>

        <div class="card-body">

            <div class="row">

                {{-- LEFT SIDE --}}
                <div class="col-md-6">

                    <table class="table table-bordered">

                        <tr>
                            <th width="50%">Agreement Value</th>
                            <td>
                                ₹ {{ number_format($booking->agreement_value ?? 0,2) }}
                            </td>
                        </tr>

                        <tr>
                            <th>Base Brokerage %</th>
                            <td>
                                {{ number_format($booking->base_brokerage_percent ?? 0,2) }}%
                            </td>
                        </tr>

                        <tr>
                            <th>Site Ladder %</th>
                            <td>
                                {{ number_format($booking->site_ladder_percent ?? 0,2) }}%
                            </td>
                        </tr>

                        <tr>
                            <th>AOP Ladder %</th>
                            <td>
                                {{ number_format($booking->aop_ladder_percent ?? 0,2) }}%
                            </td>
                        </tr>

                        <tr class="table-primary">
                            <th>Total Brokerage %</th>
                            <td>
                                <strong>
                                    {{ number_format($booking->total_brokerage_percent ?? 0,2) }}%
                                </strong>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                Revenue
                                <br>
                                <small class="text-muted">
                                    Brokerage % of Agreement Value
                                </small>
                            </th>

                            <td>
                                ₹ {{ number_format($booking->current_effective_amount ?? 0,2) }}
                            </td>
                        </tr>

                        <tr>
                            <th>Passback</th>
                            <td>
                                ₹ {{ number_format($booking->passback ?? 0,2) }}
                            </td>
                        </tr>

                        <tr>
                            <th>Additional Kicker</th>
                            <td>
                                ₹ {{ number_format($booking->additional_kicker ?? 0,2) }}
                            </td>
                        </tr>

                    </table>

                </div>


                {{-- RIGHT SIDE --}}
                <div class="col-md-6">

                    @php
                        $payments = $booking->brokeragePayments ?? collect();

                        $totalGST = $payments->sum('total_gst_amount');

                        $totalInvoiceInclGST = $payments->sum(function ($p) {
                            return ($p->invoice_amount ?? 0) + ($p->total_gst_amount ?? 0);
                        });

                        $totalTDS = $payments->sum('tds_amount');
                    @endphp

                    <table class="table table-bordered">

                        <tr class="table-success">
                            <th width="50%">
                                Final Revenue
                                <br>
                                <small class="text-muted">
                                    Revenue - Passback + Additional Kicker
                                </small>
                            </th>

                            <td>
                                <strong>
                                    ₹ {{ number_format($booking->final_revenue ?? 0,2) }}
                                </strong>
                            </td>
                        </tr>

                        <tr>
                            <th>Total GST Raised</th>
                            <td>
                                ₹ {{ number_format($totalGST,2) }}
                            </td>
                        </tr>

                        <tr>
                            <th>Total Invoice Value (Incl GST)</th>
                            <td>
                                ₹ {{ number_format($totalInvoiceInclGST,2) }}
                            </td>
                        </tr>

                        <tr>
                            <th>Total TDS Deducted</th>
                            <td>
                                ₹ {{ number_format($totalTDS,2) }}
                            </td>
                        </tr>

                        <tr>
                            <th>Amount Receivable</th>
                            <td>
                                ₹ {{ number_format($booking->amount_receivable ?? 0,2) }}
                            </td>
                        </tr>

                        <tr>
                            <th>TDS Amount</th>
                            <td>
                                ₹ {{ number_format($booking->tds_amount ?? 0,2) }}
                            </td>
                        </tr>

                        <tr>
                            <th>Total Invoice % Raised</th>
                            <td>
                                {{ number_format($booking->total_invoice_percent ?? 0,2) }}%
                            </td>
                        </tr>

                        <tr>
                            <th>Total Invoice Amount Raised</th>
                            <td>
                                ₹ {{ number_format($booking->total_invoice_amount ?? 0,2) }}
                            </td>
                        </tr>

                        <tr>
                            <th>Total Received Amount</th>
                            <td>
                                ₹ {{ number_format($booking->total_received_amount ?? 0,2) }}
                            </td>
                        </tr>

                        <tr>
                            <th>Pending Brokerage %</th>
                            <td>
                                {{ number_format($booking->pending_brokerage_percent ?? 0,2) }}%
                            </td>
                        </tr>

                        <tr>
                            <th>Pending Brokerage Amount</th>
                            <td>
                                ₹ {{ number_format($booking->pending_brokerage_amount ?? 0,2) }}
                            </td>
                        </tr>

                        <tr>
                            <th>Payment Status</th>
                            <td>

                                @if($booking->payment_status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($booking->payment_status == 'partial')
                                    <span class="badge bg-warning">Partial</span>
                                @else
                                    <span class="badge bg-danger">Pending</span>
                                @endif

                            </td>
                        </tr>

                        <tr>
                            <th>Payment Follow Up Date</th>
                            <td>
                                {{ $booking->payment_followup_date ?? '-' }}
                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>



    <div class="row">

        {{-- CLIENT DETAILS --}}
        <div class="col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-header">
                    <h5 class="mb-0">Client Details</h5>
                </div>

                <div class="card-body">

                    <table class="table table-bordered">

                        <tr>
                            <th width="35%">Client Name</th>
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

            </div>

        </div>


        {{-- PROJECT DETAILS --}}
        <div class="col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-header">
                    <h5 class="mb-0">Project Details</h5>
                </div>

                <div class="card-body">

                    <table class="table table-bordered">

                        <tr>
                            <th width="35%">Project</th>
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
                            <td>
                                ₹ {{ number_format($booking->booking_amount ?? 0,2) }}
                            </td>
                        </tr>

                        <tr>
                            <th>Agreement Value</th>
                            <td>
                                ₹ {{ number_format($booking->agreement_value ?? 0,2) }}
                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>

  
    {{-- TEAM HIERARCHY --}}
    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0 section-title">
                Team Hierarchy
            </h5>
        </div>

        <div class="card-body">

            @php

                $salesManager = optional($booking->user);

                $tl = optional($salesManager->reportingManager);

                $srTl = optional($tl->reportingManager);

                $clusterHead = optional($srTl->reportingManager);

            @endphp

            <div class="row g-3">

                <div class="col-md-3">

                    <div class="border rounded p-3 bg-label-primary h-100">

                        <small class="text-muted d-block">
                            Sales Manager
                        </small>

                        <h6 class="mb-0 mt-2">
                            {{ $salesManager->name ?? '-' }}
                        </h6>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="border rounded p-3 bg-label-info h-100">

                        <small class="text-muted d-block">
                            Team Leader
                        </small>

                        <h6 class="mb-0 mt-2">
                            {{ $tl->name ?? '-' }}
                        </h6>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="border rounded p-3 bg-label-warning h-100">

                        <small class="text-muted d-block">
                            Senior TL
                        </small>

                        <h6 class="mb-0 mt-2">
                            {{ $srTl->name ?? '-' }}
                        </h6>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="border rounded p-3 bg-label-success h-100">

                        <small class="text-muted d-block">
                            Cluster Head
                        </small>

                        <h6 class="mb-0 mt-2">
                            {{ $clusterHead->name ?? '-' }}
                        </h6>

                    </div>

                </div>

            </div>

        </div>

    </div>
   



    {{-- PAYMENT HISTORY --}}
    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="mb-0">
                Brokerage Invoice History
            </h5>

            <button class="btn btn-primary"
                    data-bs-toggle="collapse"
                    data-bs-target="#addInvoiceSection">

                Add Invoice

            </button>

        </div>

        <div class="card-body">

            @php
            $payments = $booking->brokeragePayments ?? collect();

            $totalInvoice = $payments->sum('invoice_amount');
            $totalReceived = $payments->sum('bank_received_amount');
            $totalTds = $payments->sum('tds_amount');

            $totalActualReceipt = $payments->sum(function ($payment) {
                return ($payment->invoice_amount ?? 0)
                    + ($payment->total_gst_amount ?? 0)
                    - ($payment->tds_amount ?? 0);
            });
        @endphp

            <div class="table-responsive">

                <table class="table table-bordered table-striped">

                    <thead>
                        <tr>
                            <th>Invoice Date</th>
                            <th>Invoice No</th>
                            <th>Type</th>
                            <th>%</th>
                            <th>Base Amount</th>
                            <th>GST</th>
                            <th>Grand Value</th>
                            <th>TDS</th>
                            <th>Received</th>
                            <th>Actual Receipt</th>
                            <th>Received Date</th>
                            <th>Billing Entity</th>
                            <th>Company Bank</th>
                            <th>Status</th>
                            <th>File</th>
                            <th>Remarks</th>
                        </tr>
                        </thead>

                    <tbody>

                    @forelse($payments as $payment)

                        @php
                            $grandValue = ($payment->invoice_amount ?? 0) + ($payment->total_gst_amount ?? 0);
                        @endphp

                        <tr>

                            <td>
                                {{ $payment->invoice_date
                                    ? \Carbon\Carbon::parse($payment->invoice_date)->format('d-m-Y')
                                    : '-' }}
                            </td>

                            <td>{{ $payment->invoice_number ?? '-' }}</td>

                            <td>
                                @if($payment->invoice_type=='tax_invoice')
                                    <span class="badge bg-primary">Tax Invoice</span>

                                @elseif($payment->invoice_type=='proforma')
                                    <span class="badge bg-info">Proforma</span>

                                @elseif($payment->invoice_type=='credit_note')
                                    <span class="badge bg-danger">Credit Note</span>

                                    <br>

                                    <small class="text-danger">
                                        {{ $payment->credit_note_number ?? '' }}
                                    </small>

                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                {{ number_format($payment->invoice_percent ?? 0,2) }}%
                            </td>

                            <td>
                                ₹ {{ number_format($payment->invoice_amount ?? 0,2) }}
                            </td>

                            <td>
                                CGST :
                                ₹ {{ number_format($payment->cgst_amount ?? 0,2) }}

                                <br>

                                SGST :
                                ₹ {{ number_format($payment->sgst_amount ?? 0,2) }}

                                <br>

                                <b>
                                    ₹ {{ number_format($payment->total_gst_amount ?? 0,2) }}
                                </b>
                            </td>

                            <td>
                                ₹ {{ number_format($grandValue,2) }}
                            </td>

                            <td>
                                ₹ {{ number_format($payment->tds_amount ?? 0,2) }}
                            </td>

                            <td>
                                ₹ {{ number_format($payment->bank_received_amount ?? 0,2) }}
                                <br>
                                <small class="text-muted">
                                    Incentive Collection
                                </small>
                            </td>

                            <td>
                                @php
                                    $actualReceipt =
                                        ($payment->invoice_amount ?? 0)
                                        + ($payment->total_gst_amount ?? 0)
                                        - ($payment->tds_amount ?? 0);
                                @endphp

                                ₹ {{ number_format($actualReceipt,2) }}

                                <br>

                                <small class="text-success">
                                    Bank Receipt
                                </small>
                            </td>

                            <td>
                                {{ $payment->bank_received_date
                                    ? \Carbon\Carbon::parse($payment->bank_received_date)->format('d-m-Y')
                                    : '-' }}
                            </td>

                            <td>
                                {{ optional($payment->billingEntity)->entity_name ?? '-' }}
                            </td>

                            <td>
                                @if($payment->companyBank)

                                    {{ $payment->companyBank->account_name }}

                                    <br>

                                    <small>
                                        {{ $payment->companyBank->bank_name }}
                                    </small>

                                @else
                                    -
                                @endif
                            </td>

                            <td>

                                @if($payment->status == 'received')
                                    <span class="badge bg-success">
                                        Received
                                    </span>

                                @elseif($payment->status == 'invoice_raised')
                                    <span class="badge bg-warning">
                                        Invoice Raised
                                    </span>

                                @else
                                    <span class="badge bg-secondary">
                                        Pending
                                    </span>
                                @endif

                            </td>

                            <td>

                                @if($payment->invoice_file)

                                    <a href="{{ asset('storage/'.$payment->invoice_file) }}"
                                    target="_blank"
                                    class="btn btn-sm btn-info">

                                        View

                                    </a>

                                @else

                                    -

                                @endif

                            </td>

                            <td>{{ $payment->remarks ?? '-' }}</td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="13" class="text-center">
                                No invoice history found
                            </td>
                        </tr>

                    @endforelse

                    {{-- TOTALS ROW FIXED --}}
                    <tr class="table-dark">

                        <th colspan="4">
                            Totals
                        </th>

                        <th>
                            ₹ {{ number_format($totalInvoice,2) }}
                        </th>

                        <th>-</th>

                        <th>-</th>

                        <th>
                            ₹ {{ number_format($totalTds,2) }}
                        </th>

                        <th>
                            ₹ {{ number_format($totalReceived,2) }}
                        </th>

                        <th>
                            ₹ {{ number_format($totalActualReceipt,2) }}
                        </th>

                        <th colspan="6">-</th>

                    </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>
     @php
    $creditNotes = $booking->brokeragePayments
                            ->where('invoice_type','credit_note');
    @endphp

    {{-- CREDIT NOTES --}}
    <div class="card mt-4">
        <div class="card-header">
            <h5>Credit Notes</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered">

                    <thead>
                        <tr>
                            <th>Credit Note No</th>
                            <th>Credit Note Date</th>
                            <th>Reason</th>

                            <th>Invoice No</th>
                            <th>Invoice Date</th>
                            <th>Invoice Type</th>

                            <th>Invoice Amount</th>
                            <th>GST</th>
                            <th>Grand Value</th>

                            <th>Billing Entity</th>
                            <th>Company Bank</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($creditNotes as $cn)

                        @php
                            $grandValue =
                                ($cn->invoice_amount ?? 0) +
                                ($cn->total_gst_amount ?? 0);
                        @endphp

                        <tr>

                            <td>{{ $cn->credit_note_number ?? '-' }}</td>

                            <td>
                                {{ $cn->credit_note_date
                                    ? \Carbon\Carbon::parse($cn->credit_note_date)->format('d-m-Y')
                                    : '-' }}
                            </td>

                            <td>{{ $cn->credit_note_reason ?? '-' }}</td>

                            <td>{{ $cn->invoice_number ?? '-' }}</td>

                            <td>
                                {{ $cn->invoice_date
                                    ? \Carbon\Carbon::parse($cn->invoice_date)->format('d-m-Y')
                                    : '-' }}
                            </td>

                            <td>
                                <span class="badge bg-danger">
                                    Credit Note
                                </span>
                            </td>

                            <td>₹ {{ number_format($cn->invoice_amount ?? 0,2) }}</td>

                            <td>
                                ₹ {{ number_format($cn->total_gst_amount ?? 0,2) }}
                            </td>

                            <td>
                                ₹ {{ number_format($grandValue,2) }}
                            </td>

                            <td>
                                {{ optional($cn->billingEntity)->entity_name ?? '-' }}
                            </td>

                            <td>
                                @if($cn->companyBank)
                                    {{ $cn->companyBank->account_name }}
                                    <br>
                                    <small>{{ $cn->companyBank->bank_name }}</small>
                                @else
                                    -
                                @endif
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="11" class="text-center">
                                No credit notes found
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>
            </div>

        </div>
    </div>
    <div class="row mt-4">

    <div class="col-md-3">
        <div class="card bg-label-primary">
            <div class="card-body">
                <h6>Total Invoices</h6>
                <h3>{{ $booking->brokeragePayments->count() }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-label-success">
            <div class="card-body">
                <h6>Received</h6>
                <h3>₹ {{ number_format($totalReceived ?? 0,0) }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-label-warning">
            <div class="card-body">
                <h6>Pending</h6>
                <h3>
                    ₹ {{ number_format($booking->pending_brokerage_amount ?? 0,0) }}
                </h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-label-danger">
            <div class="card-body">
                <h6>TDS Deducted</h6>
                <h3>₹ {{ number_format($totalTds ?? 0,0) }}</h3>
            </div>
        </div>
    </div>

</div>
<div class="card mt-4">

    <div class="card-header">
        <h5 class="mb-0">Payment Timeline</h5>
    </div>

    <div class="card-body">

        <ul class="list-group">

            <li class="list-group-item d-flex justify-content-between">
                <span>Booking Created</span>
                <span class="text-muted">
                    {{ optional($booking->created_at)->format('d-m-Y H:i') }}
                </span>
            </li>

            <li class="list-group-item d-flex justify-content-between">
                <span>Invoice Raised</span>
                <span>
                    @if($booking->total_invoice_amount > 0)
                        <span class="badge bg-success">Done</span>
                    @else
                        <span class="badge bg-secondary">Pending</span>
                    @endif
                </span>
            </li>

            <li class="list-group-item d-flex justify-content-between">
                <span>Partial Payment</span>
                <span>
                    @if($booking->total_received_amount > 0 && $booking->payment_status != 'completed')
                        <span class="badge bg-warning">In Progress</span>
                    @else
                        -
                    @endif
                </span>
            </li>

            <li class="list-group-item d-flex justify-content-between">
                <span>Final Payment</span>
                <span>
                    @if($booking->payment_status == 'completed')
                        <span class="badge bg-success">Completed</span>
                    @else
                        <span class="badge bg-danger">Pending</span>
                    @endif
                </span>
            </li>

        </ul>

    </div>

</div>

</div>

@endsection
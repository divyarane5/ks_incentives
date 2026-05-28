@extends('layouts.app')

@section('content')

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

            <a href="{{ route('booking.index') }}"
               class="btn btn-secondary">
                Back
            </a>

            <a href="{{ route('booking.edit',$booking->id) }}"
               class="btn btn-primary">
                Edit Booking
            </a>

        </div>

    </div>


    {{-- SUMMARY CARDS --}}
    ```blade
    {{-- ================= FINANCIAL SUMMARY ================= --}}
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
                            <th>
                                Amount Receivable
                            </th>

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

                                    <span class="badge bg-success">
                                        Completed
                                    </span>

                                @elseif($booking->payment_status == 'partial')

                                    <span class="badge bg-warning">
                                        Partial
                                    </span>

                                @else

                                    <span class="badge bg-danger">
                                        Pending
                                    </span>

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
    ```



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
            <h5 class="mb-0">Team Hierarchy</h5>
        </div>

        <div class="card-body">

            @php

                $salesManager = optional($booking->user);

                $tl = optional($salesManager->reportingManager);

                $srTl = optional($tl->reportingManager);

                $clusterHead = optional($srTl->reportingManager);

            @endphp

            <div class="row">

                <div class="col-md-3 mb-3">
                    <label class="fw-bold">Sales Manager</label>
                    <div>{{ $salesManager->name ?? '-' }}</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="fw-bold">Team Leader</label>
                    <div>{{ $tl->name ?? '-' }}</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="fw-bold">Senior TL</label>
                    <div>{{ $srTl->name ?? '-' }}</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="fw-bold">Cluster Head</label>
                    <div>{{ $clusterHead->name ?? '-' }}</div>
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

            <div class="table-responsive">

                <table class="table table-bordered table-striped">

                    <thead>

                    <tr>
                        <th>Date</th>
                        <th>Invoice %</th>
                        <th>Invoice Amount</th>
                        <th>TDS</th>
                        <th>Received</th>
                        <th>Status</th>
                        <th>File</th>
                        <th>Remarks</th>
                    </tr>

                    </thead>

                    <tbody>

                    @forelse($booking->brokeragePayments as $payment)

                        <tr>

                            <td>
                                {{ $payment->invoice_date ?? '-' }}
                            </td>

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

                                        View File

                                    </a>

                                @else
                                    -
                                @endif

                            </td>

                            <td>
                                {{ $payment->remarks ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="text-center">
                                No invoice history found
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection
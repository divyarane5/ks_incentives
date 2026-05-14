@extends('layouts.app')

@section('content')

<div class="container-xxl container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-bold mb-1">
                Employee Incentive Breakdown
            </h4>

            <small class="text-muted">
                FY : {{ $fy }}
            </small>

        </div>

        <a href="{{ route('incentives.preview') }}"
           class="btn btn-secondary">

            Back

        </a>

    </div>

    {{-- SUMMARY CARD --}}

    <div class="card mb-4">

        <div class="card-body">

            <div class="row">

                <div class="col-md-3 mb-3">

                    <label class="text-muted small">
                        Employee
                    </label>

                    <div class="fw-bold">
                        {{ $user->name }}
                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <label class="text-muted small">
                        Role
                    </label>

                    <div class="fw-bold">

                        {{ $summary->role ?? '-' }}

                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <label class="text-muted small">
                        Annual Salary
                    </label>

                    <div class="fw-bold">

                        ₹ {{ number_format($summary->annual_salary ?? 0,2) }}

                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <label class="text-muted small">
                        Collection
                    </label>

                    <div class="fw-bold text-primary">

                        ₹ {{ number_format($summary->booking_collection ?? 0,2) }}

                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <label class="text-muted small">
                        Performance
                    </label>

                    <div>

                        @if(($summary->performance_times ?? 0) >= 4)

                            <span class="badge bg-success">

                                {{ number_format($summary->performance_times ?? 0,2) }}x

                            </span>

                        @else

                            <span class="badge bg-danger">

                                {{ number_format($summary->performance_times ?? 0,2) }}x

                            </span>

                        @endif

                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <label class="text-muted small">
                        Slab
                    </label>

                    <div class="fw-bold">

                        {{ $summary->incentive_percent ?? 0 }}%

                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <label class="text-muted small">
                        Eligible Collection
                    </label>

                    <div class="fw-bold text-success">

                        ₹ {{ number_format($summary->eligible_collection ?? 0,2) }}

                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <label class="text-muted small">
                        Final Incentive
                    </label>

                    <div class="fw-bold text-success">

                        ₹ {{ number_format($summary->final_incentive ?? 0,2) }}

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- BOOKING BREAKDOWN --}}

    <div class="card">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <h5 class="mb-0">
                    Booking-wise Breakdown
                </h5>

                <div class="fw-bold text-primary">

                    Total Collection :
                    ₹ {{ number_format($total ?? 0,2) }}

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-bordered table-striped align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th width="60">
                                #
                            </th>

                            <th>
                                Booking ID
                            </th>

                            <th>
                                Client
                            </th>

                            <th class="text-end">
                                Received Amount
                            </th>

                            <th>
                                Received Date
                            </th>

                            <th class="text-center">
                                Status
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($bookings as $key => $booking)

                        <tr>

                            <td>
                                {{ $key + 1 }}
                            </td>

                            <td>

                                <span class="fw-semibold">
                                    #{{ $booking->booking_ref_id }}
                                </span>

                            </td>

                            <td>
                                {{ $booking->client_name }}
                            </td>

                            <td class="text-end fw-semibold text-primary">

                                ₹ {{ number_format($booking->bank_received_amount,2) }}

                            </td>

                            <td>

                                {{ \Carbon\Carbon::parse($booking->bank_received_date)->format('d M Y') }}

                            </td>

                            <td class="text-center">

                                <span class="badge bg-success">
                                    Received
                                </span>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="6"
                                class="text-center py-4">

                                No Collection Found

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                    @if(count($bookings) > 0)

                    <tfoot class="table-light fw-bold">

                        <tr>

                            <td colspan="3">
                                Total Collection
                            </td>

                            <td class="text-end text-primary">

                                ₹ {{ number_format($total ?? 0,2) }}

                            </td>

                            <td colspan="2"></td>

                        </tr>

                    </tfoot>

                    @endif

                </table>

            </div>

        </div>

    </div>

</div>

@endsection
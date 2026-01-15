@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">
            Booking #{{ $mandate_booking->id }}
            <small class="text-muted">
                — {{ $mandate_booking->project->project_name ?? '—' }}
            </small>
        </h4>

        <a href="{{ route('mandate_bookings.index') }}" class="btn btn-secondary">
            ← Back to Bookings
        </a>
    </div>
<div class="card mb-4">
    <h5 class="card-header">Booking and Property Details</h5>

    <div class="card-body">

        <div class="row g-3 mb-3">
            <div class="col-md-3"><strong>Booking ID:</strong> #{{ $mandate_booking->id }}</div>
            <div class="col-md-3"><strong>Booking Date:</strong> {{ \Carbon\Carbon::parse($mandate_booking->booking_date)->format('d M Y') }}</div>
            <div class="col-md-3"><strong>Project:</strong> {{ $mandate_booking->project->project_name ?? '—' }}</div>
            <div class="col-md-3"><strong>Status:</strong>
                <span class="badge bg-warning">{{ ucfirst($mandate_booking->booking_status) }}</span>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-3"><strong>Tower:</strong> {{ $mandate_booking->tower ?? '—' }}</div>
            <div class="col-md-3"><strong>Wing:</strong> {{ $mandate_booking->wing ?? '—' }}</div>
            <div class="col-md-3"><strong>Unit No:</strong> {{ $mandate_booking->unit_no ?? '—' }}</div>
            <div class="col-md-3"><strong>Floor:</strong> {{ $mandate_booking->floor_no ?? '—' }}</div>

            <div class="col-md-3"><strong>Configuration:</strong> {{ $mandate_booking->configuration ?? '—' }}</div>
            <div class="col-md-3"><strong>RERA Carpet Area:</strong> {{ $mandate_booking->rera_carpet_area ?? '—' }} sq.ft</div>
            <div class="col-md-3"><strong>Parking:</strong> {{ $mandate_booking->parking_count ?? '0' }} ({{ $mandate_booking->parking_type ?? '—' }})</div>
            <div class="col-md-3"><strong>Property Type:</strong> {{ $mandate_booking->property_type ?? '—' }}</div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-3"><strong>Booking Source:</strong> {{ $mandate_booking->booking_source ?? '—' }}</div>
            @if($mandate_booking->channel_partner_id)
                <div class="col-md-3"><strong>Channel Partner:</strong> {{ $mandate_booking->channel_partner->firm_name ?? '—' }}</div>
            @endif
            @if($mandate_booking->reference_name)
                <div class="col-md-3"><strong>Reference:</strong> {{ $mandate_booking->reference_name }}</div>
            @endif
        </div>

    </div>

    @if($mandate_booking->booking_form_file)
    <div class="card-footer">
        <a href="{{ asset('storage/'.$mandate_booking->booking_form_file) }}" target="_blank" class="btn btn-outline-primary">
            View Booking Form
        </a>
    </div>
    @endif
</div>
<div class="card mb-4">
    <h5 class="card-header">Applicant Details</h5>

    <div class="card-body">

        @forelse($mandate_booking->applicants as $applicant)

            <div class="border rounded p-3 mb-4">

                <div class="row g-3 mb-2">
                    <div class="col-md-12">
                        <span class="badge bg-primary">
                            {{ ucfirst($applicant->type) }} Applicant
                        </span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <strong>Name:</strong>
                        {{ $applicant->first_name }}
                        {{ $applicant->middle_name }}
                        {{ $applicant->last_name }}
                    </div>

                    <div class="col-md-3">
                        <strong>Mobile:</strong>
                        {{ $applicant->mobile ?? '—' }}
                    </div>

                    <div class="col-md-3">
                        <strong>Alternate Mobile:</strong>
                        {{ $applicant->alternate_mobile ?? '—' }}
                    </div>

                    <div class="col-md-3">
                        <strong>Email:</strong>
                        {{ $applicant->email ?? '—' }}
                    </div>

                    <div class="col-md-3">
                        <strong>PAN Number:</strong>
                        {{ $applicant->pan_number ?? '—' }}
                    </div>

                    <div class="col-md-3">
                        <strong>Aadhaar Number:</strong>
                        {{ $applicant->aadhar_number ?? '—' }}
                    </div>

                    <div class="col-md-3">
                        <strong>PAN Document:</strong><br>
                        @if($applicant->pan_file)
                            <a href="{{ asset('storage/'.$applicant->pan_file) }}" target="_blank">
                                View PAN
                            </a>
                        @else
                            —
                        @endif
                    </div>

                    <div class="col-md-3">
                        <strong>Aadhaar Document:</strong><br>
                        @if($applicant->aadhar_file)
                            <a href="{{ asset('storage/'.$applicant->aadhar_file) }}" target="_blank">
                                View Aadhaar
                            </a>
                        @else
                            —
                        @endif
                    </div>
                </div>

            </div>

        @empty
            <p class="text-muted mb-0">No applicant details available.</p>
        @endforelse

    </div>
</div>
<div class="card mb-4">
    <h5 class="card-header">Finance Details</h5>

    <div class="card-body">
        @if($mandate_booking->finance)

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <strong>Unit Value:</strong><br>
                    ₹ {{ number_format($mandate_booking->finance->unit_value, 2) }}
                </div>

                <div class="col-md-3">
                    <strong>Other Charges:</strong><br>
                    ₹ {{ number_format($mandate_booking->finance->other_charges, 2) }}
                </div>

                <div class="col-md-3">
                    <strong>Car Park Charges:</strong><br>
                    ₹ {{ number_format($mandate_booking->finance->car_park_charges, 2) }}
                </div>

                <div class="col-md-3">
                    <strong>Agreement Value:</strong><br>
                    ₹ {{ number_format($mandate_booking->finance->agreement_value, 2) }}
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <strong>Current Due %:</strong><br>
                    {{ number_format($mandate_booking->finance->current_due_percent, 2) }} %
                </div>

                <div class="col-md-3">
                    <strong>Registration Status:</strong><br>
                    @if($mandate_booking->finance->is_registered)
                        <span class="badge bg-success">Registered</span>
                    @else
                        <span class="badge bg-warning">Not Registered</span>
                    @endif
                </div>

                <div class="col-md-3">
                    <strong>Finance Created:</strong><br>
                    {{ $mandate_booking->finance->created_at->format('d M Y') }}
                </div>
            </div>

        @else
            <div class="alert alert-warning mb-0">
                Finance details not available for this booking.
            </div>
        @endif
    </div>
</div>
<div class="card mb-4">
    <h5 class="card-header">Payment Details</h5>

    <div class="card-body">

        @if($mandate_booking->payments->count())

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Mode</th>
                            <th>Bank</th>
                            <th>Transaction / Cheque</th>
                            <th>Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mandate_booking->payments as $index => $payment)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($payment->date)->format('d M Y') }}
                                </td>

                                <td>
                                    ₹ {{ number_format($payment->amount, 2) }}
                                </td>

                                <td>
                                    <span class="badge bg-info">
                                        {{ strtoupper($payment->mode) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $payment->bank_name ?? '—' }}
                                </td>

                                <td>
                                    @if($payment->transaction_id)
                                        TXN: {{ $payment->transaction_id }}
                                    @elseif($payment->cheque_number)
                                        CHQ: {{ $payment->cheque_number }}
                                    @else
                                        —
                                    @endif
                                </td>

                                <td>
                                    @if($payment->proof)
                                        <a href="{{ asset('storage/'.$payment->proof) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Payment Summary --}}
            <div class="row mt-3">
                <div class="col-md-4">
                    <strong>Total Paid:</strong><br>
                    ₹ {{ number_format($mandate_booking->payments->sum('amount'), 2) }}
                </div>
                <div class="col-md-4">
                    <strong>Payment Count:</strong><br>
                    {{ $mandate_booking->payments->count() }}
                </div>
            </div>

        @else
            <div class="alert alert-warning mb-0">
                No payments recorded for this booking.
            </div>
        @endif

    </div>
</div>
@if($mandate_booking->signature)
<div class="card mb-4">
    <h5 class="card-header">Consent & Signatures</h5>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <strong>Developer Consent:</strong><br>
                @if($mandate_booking->signature->developer_consent_file)
                    <a href="{{ asset('storage/'.$mandate_booking->signature->developer_consent_file) }}" target="_blank">
                        View
                    </a>
                @else
                    —
                @endif
            </div>

            <div class="col-md-4">
                <strong>Mandate Consent:</strong><br>
                @if($mandate_booking->signature->mandate_consent_file)
                    <a href="{{ asset('storage/'.$mandate_booking->signature->mandate_consent_file) }}" target="_blank">
                        View
                    </a>
                @else
                    —
                @endif
            </div>

            <div class="col-md-4">
                <strong>Channel Partner Consent:</strong><br>
                @if($mandate_booking->signature->cp_consent_file)
                    <a href="{{ asset('storage/'.$mandate_booking->signature->cp_consent_file) }}" target="_blank">
                        View
                    </a>
                @else
                    —
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<div class="card mb-4">
    <h5 class="card-header">Brokerage Eligibility</h5>

    <div class="card-body">

        @if($mandate_booking->brokerage)

            @php
                $b = $mandate_booking->brokerage;
            @endphp

            <div class="mb-3">
                @if($b->is_eligible)
                    <span class="badge bg-success">Eligible for Brokerage</span>
                @else
                    <span class="badge bg-danger">Not Eligible for Brokerage</span>
                @endif
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <strong>Agreement Value</strong><br>
                    ₹ {{ number_format($b->agreement_value, 2) }}
                </div>

                <div class="col-md-3">
                    <strong>Total Paid</strong><br>
                    ₹ {{ number_format($b->total_paid, 2) }}
                </div>

                <div class="col-md-3">
                    <strong>Payment %</strong><br>
                    {{ number_format($b->payment_percent, 2) }} %
                </div>

                <div class="col-md-3">
                    <strong>Registration</strong><br>
                    {{ $b->is_registered ? 'Completed' : 'Pending' }}
                </div>
            </div>

            <hr>

            <div class="row g-3">
                <div class="col-md-4">
                    <strong>Scenario</strong><br>
                    {{ str_replace('_', ' ', $b->eligibility_scenario) }}
                </div>

                <div class="col-md-8">
                    <strong>Reason</strong><br>
                    {{ $b->eligibility_reason }}
                </div>
            </div>

            <hr>

            <div class="row g-3">
                <div class="col-md-4">
                    <strong>Threshold %</strong><br>
                    {{ $b->threshold_percentage }} %
                </div>

                <div class="col-md-4">
                    <strong>Current Due %</strong><br>
                    {{ $b->current_due_percentage }} %
                </div>

                <div class="col-md-4">
                    <strong>Status</strong><br>
                    <span class="badge bg-warning">
                        {{ ucfirst($b->status) }}
                    </span>
                </div>
            </div>

            @if($b->brokerage_amount)
                <hr>
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong>Brokerage %</strong><br>
                        {{ $b->brokerage_percent }} %
                    </div>

                    <div class="col-md-6">
                        <strong>Brokerage Amount</strong><br>
                        ₹ {{ number_format($b->brokerage_amount, 2) }}
                    </div>
                </div>
            @endif

        @else
            <p class="text-muted mb-0">
                Brokerage eligibility not generated yet.
            </p>
        @endif

    </div>
</div>


</div>
@endsection
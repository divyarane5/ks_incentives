@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">
    <h4 class="fw-bold mb-4">Edit Booking</h4>

    {{-- ERRORS --}}
    @if ($errors->any())
    <div class="alert alert-danger">
            <h6>Validation Errors:</h6>
            <ul class="mb-0">
                @foreach ($errors->messages() as $field => $messages)
                    <li>
                        <strong>{{ $field }}</strong> :
                        {{ implode(', ', $messages) }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <form method="POST"
              action="{{ route('mandate_bookings.update', $booking->id) }}"
              enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
            @csrf
            @method('PUT')

            <div class="card-body">

                {{-- ================= TAB HEADERS ================= --}}
                <ul class="nav nav-pills mb-4" id="stepTabs">
                    @foreach(['Booking','Applicants','Finance','Consent & Signatures','Brokerage'] as $i=>$tab)
                        <li class="nav-item">
                            <button type="button"
                                    class="nav-link {{ $i==0?'active':'' }}"
                                    data-index="{{ $i }}">
                                {{ $i+1 }}. {{ $tab }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                {{-- ================= STEP 1: BOOKING ================= --}}
                <div class="step" data-step="booking">
                    <h5 class="mb-3">Booking Details</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Booking Date *</label>
                            <input type="date" name="booking_date"
                                   value="{{ old('booking_date', $booking->booking_date) }}"
                                   class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Project *</label>
                            <select name="project_id" class="form-select" required>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}"
                                        {{ old('project_id',$booking->project_id)==$project->id?'selected':'' }}>
                                        {{ $project->project_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input name="tower" class="form-control"
                                   value="{{ old('tower',$booking->tower) }}" placeholder="Tower">
                        </div>
                        <div class="col-md-4">
                            <input name="wing" class="form-control"
                                   value="{{ old('wing',$booking->wing) }}" placeholder="Wing">
                        </div>
                        <div class="col-md-4">
                            <input name="unit_no" class="form-control"
                                   value="{{ old('unit_no',$booking->unit_no) }}" placeholder="Unit No">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input name="floor_no" class="form-control"
                                   value="{{ old('floor_no',$booking->floor_no) }}" placeholder="Floor No">
                        </div>
                        <div class="col-md-4">
                            <input name="configuration" class="form-control"
                                   value="{{ old('configuration',$booking->configuration) }}" placeholder="Configuration">
                        </div>
                        <div class="col-md-4">
                            <input name="rera_carpet_area" class="form-control"
                                   value="{{ old('rera_carpet_area',$booking->rera_carpet_area) }}" placeholder="RERA Carpet Area">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <input name="parking_count" class="form-control"
                                   value="{{ old('parking_count',$booking->parking_count) }}" placeholder="No of Parking">
                        </div>
                        <div class="col-md-4">
                            <select name="parking_type" class="form-select">
                                <option value="">Parking Type</option>
                                <option {{ $booking->parking_type=='Open'?'selected':'' }}>Open</option>
                                <option {{ $booking->parking_type=='Covered'?'selected':'' }}>Covered</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="property_type" class="form-select">
                                <option value="">Property Type</option>
                                <option {{ $booking->property_type=='Residential'?'selected':'' }}>Residential</option>
                                <option {{ $booking->property_type=='Commercial'?'selected':'' }}>Commercial</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Booking Form</label>
                        <input type="file" name="booking_form_file" class="form-control">
                        @if($booking->booking_form_file)
                            <a target="_blank" href="{{ asset('storage/'.$booking->booking_form_file) }}">
                                View existing
                            </a>
                        @endif
                    </div>
                    <hr>

                    <h6 class="mb-2">Booking Source</h6>

                    <select name="booking_source"
                            id="bookingSource"
                            class="form-select mb-3">
                        <option value="">Select Source</option>

                        @foreach(['Website','Online','Hoarding','Newspaper','Exhibition','Reference','Channel Partner'] as $src)
                            <option value="{{ $src }}"
                                {{ old('booking_source', $booking->booking_source) == $src ? 'selected' : '' }}>
                                {{ $src }}
                            </option>
                        @endforeach
                    </select>

                    {{-- CHANNEL PARTNER --}}
                    <div id="sourceCP"
                        class="{{ old('booking_source', $booking->booking_source) == 'Channel Partner' ? '' : 'd-none' }} mb-3">
                        <select name="channel_partner_id" class="form-select">
                            <option value="">Select Channel Partner</option>
                            @foreach($channelPartners as $cp)
                                <option value="{{ $cp->id }}"
                                    {{ old('channel_partner_id', $booking->channel_partner_id) == $cp->id ? 'selected' : '' }}>
                                    {{ $cp->firm_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- REFERENCE --}}
                    <div id="sourceReference"
                        class="{{ old('booking_source', $booking->booking_source) == 'Reference' ? '' : 'd-none' }} mb-3">
                        <input name="reference_name"
                            value="{{ old('reference_name', $booking->reference_name) }}"
                            class="form-control mb-2"
                            placeholder="Reference Name">

                        <input name="reference_contact"
                            value="{{ old('reference_contact', $booking->reference_contact) }}"
                            class="form-control"
                            placeholder="Reference Contact">
                    </div>

                    {{-- REMARK --}}
                    <div id="sourceRemark"
                        class="{{ in_array(old('booking_source', $booking->booking_source), ['Website','Online','Hoarding','Newspaper','Exhibition']) ? '' : 'd-none' }}">
                        <textarea name="source_remark"
                                class="form-control"
                                placeholder="Remarks">{{ old('source_remark', $booking->source_remark) }}</textarea>
                    </div>

                </div>

                {{-- ================= STEP 2: APPLICANTS ================= --}}
                <div class="step d-none" data-step="applicants">
                    <h5 class="mb-3">Applicants</h5>

                    @foreach($booking->applicants as $i => $applicant)
                        @php
                            $comm = $applicant->addresses->where('address_type','communication')->first();
                            $perm = $applicant->addresses->where('address_type','permanent')->first();
                        @endphp

                        <div class="border rounded p-3 mb-4">
                            <h6 class="mb-3">
                                {{ $applicant->type == 'primary' ? 'Primary Applicant' : 'Co Applicant' }}
                            </h6>

                            <input type="hidden" name="applicants[{{ $i }}][type]" value="{{ $applicant->type }}">

                            {{-- BASIC DETAILS --}}
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label>First Name</label>
                                    <input class="form-control"
                                        name="applicants[{{ $i }}][first_name]"
                                        value="{{ $applicant->first_name }}">
                                </div>
                                <div class="col-md-4">
                                    <label>Middle Name</label>
                                    <input class="form-control"
                                        name="applicants[{{ $i }}][middle_name]"
                                        value="{{ $applicant->middle_name }}">
                                </div>
                                <div class="col-md-4">
                                    <label>Last Name</label>
                                    <input class="form-control"
                                        name="applicants[{{ $i }}][last_name]"
                                        value="{{ $applicant->last_name }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label>Mobile</label>
                                    <input class="form-control"
                                        name="applicants[{{ $i }}][mobile]"
                                        value="{{ $applicant->mobile }}">
                                </div>
                                <div class="col-md-4">
                                    <label>Alternate Mobile</label>
                                    <input class="form-control"
                                        name="applicants[{{ $i }}][alternate_mobile]"
                                        value="{{ $applicant->alternate_mobile }}">
                                </div>
                                <div class="col-md-4">
                                    <label>Email</label>
                                    <input class="form-control"
                                        name="applicants[{{ $i }}][email]"
                                        value="{{ $applicant->email }}">
                                </div>
                            </div>

                            {{-- KYC --}}
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label>PAN</label>
                                    <input class="form-control"
                                        name="applicants[{{ $i }}][pan_number]"
                                        value="{{ $applicant->pan_number }}">
                                </div>
                                <div class="col-md-3">
                                    <label>PAN File</label>
                                    <input type="file"
                                        name="applicants[{{ $i }}][pan_file]"
                                        class="form-control">
                                    @if($applicant->pan_file)
                                        <small>
                                            <a target="_blank" href="{{ asset('storage/'.$applicant->pan_file) }}">
                                                View existing
                                            </a>
                                        </small>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    <label>Aadhaar</label>
                                    <input class="form-control"
                                        name="applicants[{{ $i }}][aadhar_number]"
                                        value="{{ $applicant->aadhar_number }}">
                                </div>
                                <div class="col-md-3">
                                    <label>Aadhaar File</label>
                                    <input type="file"
                                        name="applicants[{{ $i }}][aadhar_file]"
                                        class="form-control">
                                    @if($applicant->aadhar_file)
                                        <small>
                                            <a target="_blank" href="{{ asset('storage/'.$applicant->aadhar_file) }}">
                                                View existing
                                            </a>
                                        </small>
                                    @endif
                                </div>
                            </div>

                            {{-- COMMUNICATION ADDRESS --}}
                            <h6>Communication Address</h6>
                            <input class="form-control mb-2"
                                name="applicants[{{ $i }}][addresses][communication][address]"
                                value="{{ $comm->address ?? '' }}"x
                                placeholder="Address">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <input class="form-control"
                                        name="applicants[{{ $i }}][addresses][communication][city]"
                                        value="{{ $comm->city ?? '' }}"
                                        placeholder="City">
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control"
                                        name="applicants[{{ $i }}][addresses][communication][state]"
                                        value="{{ $comm->state ?? '' }}"
                                        placeholder="State">
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control"
                                        name="applicants[{{ $i }}][addresses][communication][pincode]"
                                        value="{{ $comm->pincode ?? '' }}"
                                        placeholder="Pincode">
                                </div>
                            </div>

                            {{-- SAME AS COMM --}}
                            <div class="form-check mb-2">
                                <input type="checkbox"
                                    class="form-check-input same-address"
                                    data-index="{{ $i }}">
                                <label class="form-check-label">
                                    Same as Communication Address
                                </label>
                            </div>

                            {{-- PERMANENT ADDRESS --}}
                            <h6>Permanent Address</h6>
                            <input class="form-control mb-2 permanent-address-{{ $i }}"
                                name="applicants[{{ $i }}][addresses][permanent][address]"
                                value="{{ $perm->address ?? '' }}"
                                placeholder="Address">
                            <div class="row">
                                <div class="col-md-4">
                                    <input class="form-control permanent-city-{{ $i }}"
                                        name="applicants[{{ $i }}][addresses][permanent][city]"
                                        value="{{ $perm->city ?? '' }}"
                                        placeholder="City">
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control permanent-state-{{ $i }}"
                                        name="applicants[{{ $i }}][addresses][permanent][state]"
                                        value="{{ $perm->state ?? '' }}"
                                        placeholder="State">
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control permanent-pincode-{{ $i }}"
                                        name="applicants[{{ $i }}][addresses][permanent][pincode]"
                                        value="{{ $perm->pincode ?? '' }}"
                                        placeholder="Pincode">
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>


                {{-- ================= STEP 3: FINANCE ================= --}}
                <div class="step d-none" data-step="finance">
                    <h5 class="mb-3">Finance</h5>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Unit Consideration Value *</label>
                            <input type="number" step="0.01"
                                name="finance[unit_value]"
                                id="unitValue"
                                value="{{ $booking->finance->unit_value ?? '' }}"
                                class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Other Charges</label>
                            <input type="number" step="0.01"
                                name="finance[other_charges]"
                                id="otherCharges"
                                value="{{ $booking->finance->other_charges ?? '' }}"
                                class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Car Park Charges</label>
                            <input type="number" step="0.01"
                                name="finance[car_park_charges]"
                                id="carParkCharges"
                                value="{{ $booking->finance->car_park_charges ?? '' }}"
                                class="form-control">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Agreement Value</label>
                            <input type="number" step="0.01"
                                name="finance[agreement_value]"
                                id="agreementValue"
                                value="{{ $booking->finance->agreement_value ?? '' }}"
                                class="form-control bg-light"
                                readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Current Due (%)</label>
                            <input type="number" step="0.01"
                                name="finance[current_due_percent]"
                                value="{{ $booking->finance->current_due_percent ?? '' }}"
                                class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Project Registered</label>
                            <div class="form-check mt-2">
                                <input type="checkbox"
                                    name="is_registered"
                                    class="form-check-input"
                                    value="1"
                                    {{ ($booking->finance->is_registered ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label">Yes</label>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3">Booking Payments</h6>

                    <div id="paymentsWrapper">
                        @foreach($booking->payments as $i => $payment)
                            <div class="payment-row border rounded p-3 mb-3">
                                <input type="hidden" name="payments[{{ $i }}][id]" value="{{ $payment->id }}">

                                <div class="row mb-2">
                                    <div class="col-md-3">
                                        <label class="form-label">Booking Amount *</label>
                                        <input type="number" step="0.01"
                                            name="payments[{{ $i }}][amount]"
                                            value="{{ $payment->amount }}"
                                            class="form-control payment-amount" required>
                                    </div>

                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Payment Mode *</label>
                                        <select name="payments[{{ $i }}][mode]"
                                                class="form-select payment-mode" required>
                                            <option value="">Select</option>
                                            @php
                                            $modes = [
                                                'UPI' => 'UPI',
                                                'Card' => 'Card Swipe',
                                                'NetBanking' => 'Net Banking',
                                                'Cheque' => 'Cheque',
                                                'Cash' => 'CC'
                                            ];
                                        @endphp

                                        @foreach($modes as $value => $label)
                                            <option value="{{ $value }}" {{ $payment->mode === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                        </select>
                                    </div>


                                    <div class="col-md-3">
                                        <label class="form-label">Payment Date</label>
                                        <input type="date"
                                            name="payments[{{ $i }}][date]"
                                            value="{{ $payment->date }}"
                                            class="form-control">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Bank Name</label>
                                        <input name="payments[{{ $i }}][bank_name]"
                                            value="{{ $payment->bank_name }}"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 transaction-field {{ in_array($payment->mode, ['UPI','Card','NetBanking']) ? '' : 'd-none' }}">
                                        <label class="form-label">Transaction ID</label>
                                        <input name="payments[{{ $i }}][transaction_id]"
                                            value="{{ $payment->transaction_id }}"
                                            class="form-control">
                                    </div>

                                    <div class="col-md-4 cheque-field {{ $payment->mode === 'Cheque' ? '' : 'd-none' }}">
                                        <label class="form-label">Cheque Number</label>
                                        <input name="payments[{{ $i }}][cheque_number]"
                                            value="{{ $payment->cheque_number }}"
                                            class="form-control">
                                    </div>

                                    <div class="col-md-4 cash-proof-field {{ $payment->mode === 'Cash' ? '' : 'd-none' }}">
                                        <label class="form-label">CC Proof</label>
                                        <input type="file" name="payments[{{ $i }}][proof]" class="form-control">
                                        @if(!empty($payment->proof))
                                            <div class="mb-1">
                                                <a href="{{ asset('storage/' . $payment->proof) }}" target="_blank">
                                                    View Existing Proof
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-outline-primary" id="addPaymentBtn">
                        + Add Another Payment
                    </button>

                    <template id="paymentTemplate">
                        <div class="payment-row border rounded p-3 mb-3">
                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <label class="form-label">Booking Amount *</label>
                                    <input type="number" step="0.01"
                                        name="payments[__INDEX__][amount]"
                                        class="form-control payment-amount" required>
                                </div>

                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Payment Mode *</label>
                                    <select name="payments[__INDEX__][mode]"
                                            class="form-select payment-mode" required>
                                        <option value="">Select</option>
                                        <option value="UPI">UPI</option>
                                        <option value="Card">Card Swipe</option>
                                        <option value="NetBanking">Net Banking</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="Cash">CC</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Payment Date</label>
                                    <input type="date"
                                        name="payments[__INDEX__][date]"
                                        class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Bank Name</label>
                                    <input name="payments[__INDEX__][bank_name]"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 transaction-field d-none">
                                    <label class="form-label">Transaction ID</label>
                                    <input name="payments[__INDEX__][transaction_id]"
                                        class="form-control">
                                </div>

                                <div class="col-md-4 cheque-field d-none">
                                    <label class="form-label">Cheque Number</label>
                                    <input name="payments[__INDEX__][cheque_number]"
                                        class="form-control">
                                </div>

                                <div class="col-md-4 cash-proof-field d-none">
                                    <label class="form-label">CC Proof</label>
                                    <input type="file"
                                        name="payments[__INDEX__][proof]"
                                        class="form-control">
                                        
                                    </div>
                            </div>
                        </div>
                    </template>


                <hr>

                <div class="mt-3">
                    <p class="fw-bold mb-1">
                        Total Paid: <span id="totalPaid">{{ $booking->payments->sum('amount') }}</span>
                    </p>
                    <p class="fw-bold">
                        Balance Amount:
                        <span id="balanceAmount">
                            {{ ($booking->finance->agreement_value ?? 0) - $booking->payments->sum('amount') }}
                        </span>
                    </p>
                </div>
                </div> {{-- end finance tab --}}

                {{-- ================= STEP 4: CONSENT ================= --}}
                <div class="step d-none" data-step="consent">
                    <h5 class="mb-3">Consent</h5>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="mb-3">Consent Documents</h6>

                            <div class="row mb-3">
                                {{-- Developer Consent --}}
                                <div class="col-md-4">
                                    <label class="form-label">Developer Consent</label>
                                    <input type="file"
                                        name="developer_consent_file"
                                        class="form-control">

                                    @if($booking->signature?->developer_consent_file)
                                        <small class="text-success d-block mt-1">
                                            Existing:
                                            <a href="{{ asset('storage/'.$booking->signature->developer_consent_file) }}"
                                            target="_blank">
                                                View File
                                            </a>
                                        </small>
                                    @endif
                                </div>

                                {{-- Mandate Consent --}}
                                <div class="col-md-4">
                                    <label class="form-label">Mandate Consent (Our Consent)</label>
                                    <input type="file"
                                        name="mandate_consent_file"
                                        class="form-control">

                                    @if($booking->signature?->mandate_consent_file)
                                        <small class="text-success d-block mt-1">
                                            Existing:
                                            <a href="{{ asset('storage/'.$booking->signature->mandate_consent_file) }}"
                                            target="_blank">
                                                View File
                                            </a>
                                        </small>
                                    @endif
                                </div>

                                {{-- Channel Partner Consent --}}
                                <div class="col-md-4">
                                    <label class="form-label">Channel Partner Consent</label>
                                    <input type="file"
                                        name="cp_consent_file"
                                        class="form-control">

                                    @if($booking->signature?->cp_consent_file)
                                        <small class="text-success d-block mt-1">
                                            Existing:
                                            <a href="{{ asset('storage/'.$booking->signature->cp_consent_file) }}"
                                            target="_blank">
                                                View File
                                            </a>
                                        </small>
                                    @endif
                                </div>
                            </div>
                           
                        </div>
                    </div>

                    {{-- OPTIONAL: SIGNATURES (ENABLE LATER IF NEEDED) --}}
                    {{-- 
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3">Signatures</h6>

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Primary Applicant Signature</label>
                                    <input type="file" name="primary_applicant_signature" class="form-control">

                                    @if($booking->signature?->primary_applicant_signature)
                                        <small class="text-success">
                                            <a href="{{ asset('storage/'.$booking->signature->primary_applicant_signature) }}"
                                            target="_blank">View</a>
                                        </small>
                                    @endif
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Co-Applicant Signature</label>
                                    <input type="file" name="co_applicant_signature" class="form-control">

                                    @if($booking->signature?->co_applicant_signature)
                                        <small class="text-success">
                                            <a href="{{ asset('storage/'.$booking->signature->co_applicant_signature) }}"
                                            target="_blank">View</a>
                                        </small>
                                    @endif
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Sales Executive Signature</label>
                                    <input type="file" name="sales_signature" class="form-control">

                                    @if($booking->signature?->sales_signature)
                                        <small class="text-success">
                                            <a href="{{ asset('storage/'.$booking->signature->sales_signature) }}"
                                            target="_blank">View</a>
                                        </small>
                                    @endif
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Closing Manager Signature</label>
                                    <input type="file" name="closing_signature" class="form-control">

                                    @if($booking->signature?->closing_signature)
                                        <small class="text-success">
                                            <a href="{{ asset('storage/'.$booking->signature->closing_signature) }}"
                                            target="_blank">View</a>
                                        </small>
                                    @endif
                                </div>
                            </div>
                        
                        </div>
                    </div>
                    --}}

                    <p class="text-muted small mt-3">
                        Uploading a new file will replace the existing document.
                        All documents are securely stored and linked to this booking.
                    </p>
                </div>

                {{-- ================= STEP 5: BROKERAGE ================= --}}
                {{-- ================= STEP 5: BROKERAGE ================= --}}
                <div class="step d-none" data-step="brokerage">
                    <h5 class="mb-3">Brokerage Eligibility</h5>

                    @if($booking->brokerage)

                        {{-- STATUS BOX --}}
                        <div class="alert {{ $booking->brokerage->is_eligible ? 'alert-success' : 'alert-warning' }}">
                            <h6 class="mb-2">
                                Brokerage Status:
                                <span class="fw-bold">
                                    {{ $booking->brokerage->is_eligible ? 'Eligible' : 'Not Eligible' }}
                                </span>
                            </h6>

                            <p class="mb-1">
                                <strong>Scenario:</strong>
                                {{ $booking->brokerage->eligibility_scenario ?? '—' }}
                            </p>

                            <p class="mb-0">
                                <strong>Reason:</strong>
                                {{ $booking->brokerage->eligibility_reason ?? '—' }}
                            </p>
                        </div>

                        {{-- SNAPSHOT --}}
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="mb-3">Payment Snapshot</h6>

                                <table class="table table-sm table-bordered w-75">
                                    <tr>
                                        <th>Total Agreement Value</th>
                                        <td>{{ number_format($booking->brokerage->agreement_value, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Paid</th>
                                        <td>{{ number_format($booking->brokerage->total_paid, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Completion %</th>
                                        <td>{{ $booking->brokerage->payment_percent }}%</td>
                                    </tr>
                                    <tr>
                                        <th>Threshold %</th>
                                        <td>{{ $booking->brokerage->threshold_percentage }}%</td>
                                    </tr>
                                    <tr>
                                        <th>Current Due %</th>
                                        <td>{{ $booking->brokerage->current_due_percentage }}%</td>
                                    </tr>
                                    <tr>
                                        <th>Project Registered</th>
                                        <td>
                                            {{ $booking->brokerage->is_registered ? 'Yes' : 'No' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        

                        {{-- BROKERAGE AMOUNT --}}
                        <div class="card border">
                                <div class="card-body">
                                    <h6 class="mb-3">Brokerage Details</h6>

                                    <table class="table table-sm table-bordered w-50">
                                        <tr>
                                            <th>Brokerage %</th>
                                            <td>
                                                {{ $booking->brokerage->brokerage_percent ?? '—' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Brokerage Amount</th>
                                            <td>
                                                {{ $booking->brokerage->brokerage_amount
                                                    ? number_format($booking->brokerage->brokerage_amount, 2)
                                                    : '—' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>{{ ucfirst($booking->brokerage->status) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Eligible At</th>
                                            <td>{{ $booking->brokerage->eligible_at ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Paid At</th>
                                            <td>{{ $booking->brokerage->paid_at ?? '—' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            {{-- INFO --}}
                            <p class="text-muted small mt-3">
                                Brokerage eligibility and payout are system calculated based on
                                project configuration, payment completion, and registration status.
                            </p>

                        @else

                                {{-- NO BROKERAGE RECORD --}}
                                <div class="alert alert-secondary">
                                    <h6 class="mb-2">Brokerage Status: Pending Evaluation</h6>
                                    <p class="mb-0">
                                        Brokerage will be evaluated automatically after payments and finance data are completed.
                                    </p>
                                </div>

                            @endif
                            <div class="mb-3">
                            <label for="bill_copy" class="form-label">Upload Bill Copy</label>
                            <input type="file" name="bill_copy" id="bill_copy" class="form-control">
                            @if($booking->brokerage->bill_copy)
                                <p class="mt-1">
                                    Uploaded: 
                                    <a href="{{ asset('storage/'.$booking->brokerage->bill_copy) }}" target="_blank">View Bill Copy</a>
                                </p>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="acceptance_copy" class="form-label">Upload Acceptance Copy</label>
                            <input type="file" name="acceptance_copy" id="acceptance_copy" class="form-control">
                            @if($booking->brokerage->acceptance_copy)
                                <p class="mt-1">
                                    Uploaded: 
                                    <a href="{{ asset('storage/'.$booking->brokerage->acceptance_copy) }}" target="_blank">View Acceptance Copy</a>
                                </p>
                            @endif
                        </div>
                        </div>
                        


                                {{-- ================= BUTTONS ================= --}}
                                <div class="d-flex justify-content-between mt-4" id="stepButtons">
                                    <button type="button" class="btn btn-secondary" id="prevBtn" >
                                        Previous
                                    </button>

                                    <button type="button" class="btn btn-primary" id="nextBtn">
                                        Next
                                    </button>

                                    <button type="submit" class="btn btn-success d-none" id="submitBtn">
                                        Update Booking
                                    </button>
                                </div>


                            </div>
                        </form>
                    </div>
                </div>
@endsection
@section('script')
<script>
    // Show/hide fields based on payment mode
    function handlePaymentMode(selectEl) {
        const row = selectEl.closest('.payment-row');
        const mode = selectEl.value;

        row.querySelector('.transaction-field')?.classList.add('d-none');
        row.querySelector('.cheque-field')?.classList.add('d-none');
        row.querySelector('.cash-proof-field')?.classList.add('d-none');

        if (['UPI', 'Card', 'NetBanking'].includes(mode)) {
            row.querySelector('.transaction-field')?.classList.remove('d-none');
        }

        if (mode === 'Cheque') {
            row.querySelector('.cheque-field')?.classList.remove('d-none');
        }

        if (mode === 'Cash') {
            row.querySelector('.cash-proof-field')?.classList.remove('d-none');
        }
    }

    // Initialize all payment rows on page load
    function initPaymentRows() {
        document.querySelectorAll('.payment-mode').forEach(select => {
            handlePaymentMode(select);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {

        // ------------------- STEP NAVIGATION -------------------
        let step = 0;
        const steps = document.querySelectorAll('.step');
        const tabs = document.querySelectorAll('#stepTabs .nav-link');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        const submitStepIndex = steps.length - 1;

        function showStep() {
            steps.forEach((s, i) => s.classList.toggle('d-none', i !== step));
            tabs.forEach((t, i) => t.classList.toggle('active', i === step));
            prevBtn.style.display = step === 0 ? 'none' : 'inline-block';
            nextBtn.style.display = step === submitStepIndex ? 'none' : 'inline-block';
            submitBtn.classList.toggle('d-none', step !== submitStepIndex);
        }

        showStep();

        prevBtn.addEventListener('click', () => { step--; showStep(); });
        nextBtn.addEventListener('click', () => {
            if (validateStep(step)) {
                step++;
                showStep();

                if (steps[step].dataset.step === 'finance') {
                    initPaymentRows();
                }
            }
        });

        // ------------------- STEP VALIDATION -------------------
        function validateStep(currentStep) {
            let valid = true;
            const stepDiv = steps[currentStep];
            const requiredFields = stepDiv.querySelectorAll('[required]');

            requiredFields.forEach(input => {
                input.classList.remove('is-invalid');
                if (!input.value) {
                    input.classList.add('is-invalid');
                    valid = false;
                }
            });

            if (!valid) alert('Please fill all required fields before proceeding.');
            return valid;
        }

        // ------------------- PAYMENT MODE CHANGE -------------------
        document.getElementById('paymentsWrapper').addEventListener('change', function(e) {
            if (e.target.classList.contains('payment-mode')) {
                handlePaymentMode(e.target);
            }
        });

        // ------------------- ADD PAYMENT ROW -------------------
        let paymentIndex = document.querySelectorAll('.payment-row').length;

        document.getElementById('addPaymentBtn').addEventListener('click', function () {
            let template = document.getElementById('paymentTemplate').innerHTML;
            template = template.replace(/__INDEX__/g, paymentIndex);

            document.getElementById('paymentsWrapper')
                .insertAdjacentHTML('beforeend', template);

            const newRow = document.querySelectorAll('.payment-row')[paymentIndex];
            const modeSelect = newRow.querySelector('.payment-mode');

            // Initialize fields based on mode (default is empty)
            handlePaymentMode(modeSelect);

            paymentIndex++;
        });

        // ------------------- INIT EDIT MODE -------------------
        initPaymentRows();

        // ------------------- AGREEMENT VALUE CALCULATION -------------------
        function calculateAgreementValue() {
            const unit = parseFloat(document.getElementById('unitValue').value) || 0;
            const other = parseFloat(document.getElementById('otherCharges').value) || 0;
            const car = parseFloat(document.getElementById('carParkCharges').value) || 0;
            const total = unit + other + car;

            document.getElementById('agreementValue').value = total.toFixed(2);
            document.getElementById('bsAgreementValue').innerText = total.toFixed(2);

            updatePaymentSummary();
        }

        ['unitValue', 'otherCharges', 'carParkCharges'].forEach(id => {
            document.getElementById(id)?.addEventListener('input', calculateAgreementValue);
        });

        // ------------------- PAYMENT SUMMARY -------------------
        function updatePaymentSummary() {
            let totalPaid = 0;
            document.querySelectorAll('.payment-amount').forEach(input => {
                totalPaid += parseFloat(input.value) || 0;
            });

            const agreementValue = parseFloat(document.getElementById('agreementValue').value) || 0;
            document.getElementById('totalPaid').innerText = totalPaid.toFixed(2);
            document.getElementById('balanceAmount').innerText = (agreementValue - totalPaid).toFixed(2);

            document.getElementById('bsTotalPaid').innerText = totalPaid.toFixed(2);
            document.getElementById('bsPaymentPercent').innerText = agreementValue ? ((totalPaid / agreementValue) * 100).toFixed(2) + '%' : '0%';
        }

        document.getElementById('paymentsWrapper').addEventListener('input', updatePaymentSummary);

        // ------------------- SAME ADDRESS COPY -------------------
        document.querySelectorAll('.same-address').forEach(cb => {
            cb.addEventListener('change', function () {
                const key = this.dataset.key;
                const comm = document.querySelectorAll(`[name^="applicants[${key}][addresses][communication]"]`);
                const perm = document.querySelectorAll(`[name^="applicants[${key}][addresses][permanent]"]`);
                if (this.checked) comm.forEach((el, i) => perm[i].value = el.value);
                else perm.forEach(el => el.value = '');
            });
        });

        // ------------------- BOOKING SOURCE -------------------
        const bookingSource = document.getElementById('bookingSource');
        bookingSource?.addEventListener('change', e => {
            document.getElementById('sourceCP').classList.add('d-none');
            document.getElementById('sourceReference').classList.add('d-none');
            document.getElementById('sourceRemark').classList.add('d-none');

            if (e.target.value === 'Channel Partner') document.getElementById('sourceCP').classList.remove('d-none');
            else if (e.target.value === 'Reference') document.getElementById('sourceReference').classList.remove('d-none');
            else if (e.target.value) document.getElementById('sourceRemark').classList.remove('d-none');
        });

    });
</script>
@endsection

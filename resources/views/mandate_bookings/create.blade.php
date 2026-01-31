@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">
    <h4 class="fw-bold mb-4">Create Booking</h4>
    {{-- GLOBAL ERRORS --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FLASH MESSAGES --}}
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="card mb-4">
        <form method="POST" action="{{ route('mandate_bookings.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">

                {{-- ================= TAB HEADERS ================= --}}
                <ul class="nav nav-pills mb-4" id="stepTabs">
                    @foreach(['Booking','Applicants','Finance','Consent & Signatures','Brokerage'] as $i=>$tab)
                        <li class="nav-item">
                            <span class="nav-link {{ $i==0?'active':'' }}">{{ $i+1 }}. {{ $tab }}</span>
                        </li>
                    @endforeach
                </ul>

                {{-- ================= STEP 1: BOOKING ================= --}}
                <div class="step" data-step="booking">
                    <h5 class="mb-3">Booking Details</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Booking Date *</label>
                            <input type="date" name="booking_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Project *</label>
                            <select name="project_id" class="form-select" required>
                                <option value="">Select</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><input name="tower" class="form-control" placeholder="Tower"></div>
                        <div class="col-md-4"><input name="wing" class="form-control" placeholder="Wing"></div>
                        <div class="col-md-4"><input name="unit_no" class="form-control" placeholder="Unit No"></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><input name="floor_no" class="form-control" placeholder="Floor No"></div>
                        <div class="col-md-4"><input name="configuration" class="form-control" placeholder="Configuration"></div>
                        <div class="col-md-4"><input name="rera_carpet_area" class="form-control" placeholder="RERA Carpet Area"></div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4"><input name="parking_count" class="form-control" placeholder="No of Parking"></div>
                        <div class="col-md-4">
                            <select name="parking_type" class="form-select">
                                <option value="">Parking Type</option>
                                <option>Open</option>
                                <option>Covered</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="property_type" class="form-select">
                                <option value="">Property Type</option>
                                <option>Residential</option>
                                <option>Commercial</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Upload Booking Form</label>
                        <input type="file" name="booking_form_file" class="form-control">
                    </div>

                    <hr>

                    <h6 class="mb-2">Booking Source</h6>
                    <select name="booking_source" id="bookingSource" class="form-select mb-3">
                        <option value="">Select Source</option>
                        <option>Website</option>
                        <option>Online</option>
                        <option>Hoarding</option>
                        <option>Newspaper</option>
                        <option>Exhibition</option>
                        <option>Reference</option>
                        <option>Channel Partner</option>
                    </select>

                    <div id="sourceCP" class="d-none mb-3">
                        <select name="channel_partner_id" class="form-select">
                            <option value="">Select Channel Partner</option>
                            @foreach($channelPartners as $cp)
                                <option value="{{ $cp->id }}">{{ $cp->firm_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="sourceReference" class="d-none mb-3">
                        <input name="reference_name" class="form-control mb-2" placeholder="Reference Name">
                        <input name="reference_contact" class="form-control" placeholder="Reference Contact">
                    </div>

                    <div id="sourceRemark" class="d-none">
                        <textarea name="source_remark" class="form-control" placeholder="Remarks"></textarea>
                    </div>
                </div>

                {{-- ================= STEP 2: APPLICANTS ================= --}}
                <div class="step d-none" data-step="applicants">
                    <h5 class="mb-3">Applicants</h5>
                    @foreach(['primary'=>'Primary Applicant','co'=>'Co Applicant'] as $key=>$label)
                        <div class="border rounded p-3 mb-4">
                            <h6>{{ $label }}</h6>
                            <input type="hidden" name="applicants[{{ $key }}][type]" value="{{ $key }}">

                            <div class="row mb-3">
                                <div class="col-md-4"><input name="applicants[{{ $key }}][first_name]" class="form-control" placeholder="First Name"></div>
                                <div class="col-md-4"><input name="applicants[{{ $key }}][middle_name]" class="form-control" placeholder="Middle Name"></div>
                                <div class="col-md-4"><input name="applicants[{{ $key }}][last_name]" class="form-control" placeholder="Last Name"></div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4"><input name="applicants[{{ $key }}][mobile]" class="form-control" placeholder="Mobile"></div>
                                <div class="col-md-4"><input name="applicants[{{ $key }}][alternate_mobile]" class="form-control" placeholder="Alternate Mobile"></div>
                                <div class="col-md-4"><input name="applicants[{{ $key }}][email]" class="form-control" placeholder="Email"></div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6"><input name="applicants[{{ $key }}][pan_number]" class="form-control" placeholder="PAN"></div>
                                <div class="col-md-6"><input name="applicants[{{ $key }}][aadhar_number]" class="form-control" placeholder="Aadhaar"></div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6"><input type="file" name="applicants[{{ $key }}][pan_file]" class="form-control"></div>
                                <div class="col-md-6"><input type="file" name="applicants[{{ $key }}][aadhar_file]" class="form-control"></div>
                            </div>

                            <h6 class="mt-3">Communication Address</h6>
                            <input name="applicants[{{ $key }}][addresses][communication][address]" class="form-control mb-2" placeholder="Address">
                            <input name="applicants[{{ $key }}][addresses][communication][city]" class="form-control mb-2" placeholder="City">
                            <input name="applicants[{{ $key }}][addresses][communication][state]" class="form-control mb-2" placeholder="State">
                            <input name="applicants[{{ $key }}][addresses][communication][pincode]" class="form-control mb-3" placeholder="Pincode">

                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input same-address" data-key="{{ $key }}">
                                <label class="form-check-label">Same as Communication Address</label>
                            </div>

                            <h6>Permanent Address</h6>
                            <input name="applicants[{{ $key }}][addresses][permanent][address]" class="form-control mb-2" placeholder="Address">
                            <input name="applicants[{{ $key }}][addresses][permanent][city]" class="form-control mb-2" placeholder="City">
                            <input name="applicants[{{ $key }}][addresses][permanent][state]" class="form-control mb-2" placeholder="State">
                            <input name="applicants[{{ $key }}][addresses][permanent][pincode]" class="form-control" placeholder="Pincode">
                        </div>
                    @endforeach
                </div>

                {{-- ================= STEP 3: FINANCE ================= --}}
                <div class="step d-none" data-step="finance">
                    <h5 class="mb-3">Finance</h5>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Unit Consideration Value *</label>
                            <input type="number" step="0.01" name="finance[unit_value]" id="unitValue" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Other Charges</label>
                            <input type="number" step="0.01" name="finance[other_charges]" id="otherCharges" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Car Park Charges</label>
                            <input type="number" step="0.01" name="finance[car_park_charges]" id="carParkCharges" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Agreement Value</label>
                            <input type="number" step="0.01" name="finance[agreement_value]" id="agreementValue" class="form-control bg-light" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Current Due (%)</label>
                            <input type="number" step="0.01" name="finance[current_due_percent]" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Project Registered</label>
                            <div class="form-check">
                                <input type="checkbox" name="is_registered" class="form-check-input" value="1" {{ old('is_registered', $mandateProject->is_registered ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label">Yes</label>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-3">Booking Payments</h6>
                    <div id="paymentsWrapper">
                        <div class="payment-row border rounded p-3 mb-3">
                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <label class="form-label">Booking Amount *</label>
                                    <input type="number" step="0.01" name="payments[0][amount]" class="form-control payment-amount">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Payment Mode *</label>
                                    <select name="payments[0][mode]" class="form-select payment-mode">
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
                                    <input type="date" name="payments[0][date]" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Bank Name</label>
                                    <input name="payments[0][bank_name]" class="form-control">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 transaction-field d-none">
                                    <label class="form-label">Transaction ID</label>
                                    <input name="payments[0][transaction_id]" class="form-control">
                                </div>
                                <div class="col-md-4 cheque-field d-none">
                                    <label class="form-label">Cheque Number</label>
                                    <input name="payments[0][cheque_number]" class="form-control">
                                </div>
                                <div class="col-md-4 cash-proof-field d-none">
                                    <label class="form-label">Upload Proof</label>
                                    <input type="file" name="payments[0][proof]" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <button type="button" class="btn btn-outline-primary" id="addPaymentBtn">+ Add Another Payment</button> -->

                    <hr>

                    <div class="mt-3">
                        <p class="fw-bold mb-1">Total Paid: <span id="totalPaid">0</span></p>
                        <p class="fw-bold">Balance Amount: <span id="balanceAmount">0</span></p>
                    </div>
                </div>

                {{-- ================= STEP 4: CONSENT ================= --}}
                <div class="step d-none" data-step="consent">
                    <h5 class="mb-3">Consent</h5>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="mb-3">Consent Documents</h6>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Developer Consent</label>
                                    <input type="file" name="developer_consent_file" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Mandate Consent (Our Consent)</label>
                                    <input type="file" name="mandate_consent_file" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Channel Partner Consent</label>
                                    <input type="file" name="cp_consent_file" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================= MANAGER ASSIGNMENT ================= --}}
                    <div class="card mt-4">
                        <div class="card-body">
                            <h6 class="mb-3">Manager Assignment</h6>

                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Sourcing Manager</label>
                                    <select name="sourcing_manager_id" class="form-select">
                                        <option value="">Select</option>
                                        @foreach($managers as $manager)
                                            <option value="{{ $manager->id }}">
                                                {{ $manager->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Pre-Sales</label>
                                    <select name="presales_id" class="form-select">
                                        <option value="">Select</option>
                                        @foreach($managers as $manager)
                                            <option value="{{ $manager->id }}">
                                                {{ $manager->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Closing Manager</label>
                                    <select name="closing_manager_id" class="form-select">
                                        <option value="">Select</option>
                                        @foreach($managers as $manager)
                                            <option value="{{ $manager->id }}">
                                                {{ $manager->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3">Signatures</h6>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Primary Applicant Signature</label>
                                    <input type="file" name="primary_applicant_signature" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Co-Applicant Signature</label>
                                    <input type="file" name="co_applicant_signature" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Sales Executive Signature</label>
                                    <input type="file" name="sales_signature" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Closing Manager Signature</label>
                                    <input type="file" name="closing_signature" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <p class="text-muted small mt-3">
                        All uploaded documents and signatures will be securely stored and associated with this booking.
                    </p>
                </div>

                {{-- ================= STEP 5: BROKERAGE ================= --}}
                <div class="step d-none" data-step="brokerage">
                    <h5 class="mb-3">Brokerage Eligibility</h5>

                    <div class="alert alert-secondary" id="brokerageStatusBox">
                        <h6 class="mb-2">
                            Brokerage Status: ⏳  <span id="brokerageStatus" class="fw-bold text-danger">Pending Evaluation</span>
                        </h6>
                        <p class="mb-1" id="brokerageReason">Brokerage eligibility will be evaluated after the booking is saved,
                        based on project configuration and payment records.</p>
                    </div>

                    <div class="card border">
                        <div class="card-body">
                            <h6 class="mb-3">Eligibility Conditions</h6>
                            <ul class="mb-0">
                                <li>
                                    <strong>Scenario 1:</strong> Payment completed up to <strong>Threshold %</strong> AND Registration completed
                                </li>
                                <li class="mt-2">
                                    <strong>Scenario 2:</strong> Total payment completed up to <strong>Current Due %</strong>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6>Payment Snapshot</h6>
                        <table class="table table-sm table-bordered w-75">
                            <tr><th>Total Agreement Value</th><td id="bsAgreementValue">—</td></tr>
                            <tr><th>Total Payment Received</th><td id="bsTotalPaid">—</td></tr>
                            <tr><th>Payment Completion %</th><td id="bsPaymentPercent">—</td></tr>
                            <tr><th>Registration Status</th><td id="bsRegistrationStatus">Pending</td></tr>
                        </table>
                    </div>

                    <div class="text-muted mt-3 small">
                        Brokerage eligibility is auto-calculated based on project configuration and payment completion. No manual action required.
                    </div>
                </div>

                {{-- ================= NAVIGATION BUTTONS ================= --}}
                <div class="mt-4">
                    <button type="button" class="btn btn-secondary" id="prevBtn">Back</button>
                    <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                    <button type="submit" class="btn btn-success d-none" id="submitBtn">Submit</button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================
       STEP NAVIGATION
    ===================================================== */
    let step = 0;
    const steps = document.querySelectorAll('.step');
    const tabs = document.querySelectorAll('#stepTabs .nav-link');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    function showStep() {
        steps.forEach((s, i) => s.classList.toggle('d-none', i !== step));
        tabs.forEach((t, i) => t.classList.toggle('active', i === step));

        prevBtn.style.display = step === 0 ? 'none' : 'inline-block';
        nextBtn.style.display = step === steps.length - 1 ? 'none' : 'inline-block';
        submitBtn.classList.toggle('d-none', step !== steps.length - 1);
    }

    showStep();

    prevBtn.addEventListener('click', () => {
        step--;
        showStep();
    });

    nextBtn.addEventListener('click', () => {
        if (validateStep(step)) {
            step++;
            showStep();
        }
    });

    /* =====================================================
       STEP VALIDATION (FIXED)
    ===================================================== */
    function validateStep(currentStep) {
        let valid = true;
        const stepDiv = steps[currentStep];

        stepDiv.querySelectorAll('[required]').forEach(input => {
            input.classList.remove('is-invalid');

            if (
                input.disabled ||
                input.closest('.d-none') ||
                input.offsetParent === null
            ) return;

            if (!input.value) {
                input.classList.add('is-invalid');
                valid = false;
            }
        });

        if (!valid) alert('Please fill all required fields.');
        return valid;
    }

    /* =====================================================
       BOOKING SOURCE LOGIC
    ===================================================== */
    function el(id) { return document.getElementById(id); }

    function handleBookingSource(value) {
        el('sourceCP')?.classList.add('d-none');
        el('sourceReference')?.classList.add('d-none');
        el('sourceRemark')?.classList.add('d-none');

        if (value === 'Channel Partner') el('sourceCP')?.classList.remove('d-none');
        else if (value === 'Reference') el('sourceReference')?.classList.remove('d-none');
        else if (value) el('sourceRemark')?.classList.remove('d-none');
    }

    el('bookingSource')?.addEventListener('change', function () {
        handleBookingSource(this.value);
    });

    if (el('bookingSource')?.value) {
        handleBookingSource(el('bookingSource').value);
    }

    /* =====================================================
       SAME ADDRESS COPY
    ===================================================== */
    document.querySelectorAll('.same-address').forEach(cb => {
        cb.addEventListener('change', function () {
            const key = this.dataset.key;
            const comm = document.querySelectorAll(`[name^="applicants[${key}][addresses][communication]"]`);
            const perm = document.querySelectorAll(`[name^="applicants[${key}][addresses][permanent]"]`);

            if (this.checked) {
                comm.forEach((el, i) => perm[i].value = el.value);
            } else {
                perm.forEach(el => el.value = '');
            }
        });
    });

    /* =====================================================
       PAYMENT MODE HANDLING
    ===================================================== */
    function handlePaymentMode(selectEl) {
        const row = selectEl.closest('.payment-row');
        if (!row) return;

        row.querySelector('.transaction-field')?.classList.add('d-none');
        row.querySelector('.cheque-field')?.classList.add('d-none');
        row.querySelector('.cash-proof-field')?.classList.add('d-none');

        const mode = selectEl.value;

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

    document.getElementById('paymentsWrapper')?.addEventListener('change', function (e) {
        if (e.target.classList.contains('payment-mode')) {
            handlePaymentMode(e.target);
        }
    });

    /* =====================================================
       AGREEMENT VALUE CALCULATION (FINAL FIX)
    ===================================================== */
    function calculateAgreementValue() {
        const unit = parseFloat(el('unitValue')?.value) || 0;
        const other = parseFloat(el('otherCharges')?.value) || 0;
        const car = parseFloat(el('carParkCharges')?.value) || 0;

        const total = unit + other + car;

        if (el('agreementValue')) el('agreementValue').value = total.toFixed(2);
        if (el('bsAgreementValue')) el('bsAgreementValue').innerText = total.toFixed(2);

        updatePaymentSummary();
    }

    document.addEventListener('input', function (e) {
        if (
            e.target.id === 'unitValue' ||
            e.target.id === 'otherCharges' ||
            e.target.id === 'carParkCharges'
        ) {
            calculateAgreementValue();
        }
    });

    /* =====================================================
       PAYMENT SUMMARY
    ===================================================== */
    function updatePaymentSummary() {
        let totalPaid = 0;
        document.querySelectorAll('.payment-amount').forEach(input => {
            totalPaid += parseFloat(input.value) || 0;
        });

        const agreementValue = parseFloat(el('agreementValue')?.value) || 0;

        el('totalPaid') && (el('totalPaid').innerText = totalPaid.toFixed(2));
        el('balanceAmount') && (el('balanceAmount').innerText = (agreementValue - totalPaid).toFixed(2));
        el('bsTotalPaid') && (el('bsTotalPaid').innerText = totalPaid.toFixed(2));
        el('bsPaymentPercent') && (
            el('bsPaymentPercent').innerText =
                agreementValue ? ((totalPaid / agreementValue) * 100).toFixed(2) + '%' : '0%'
        );
    }

    document.getElementById('paymentsWrapper')?.addEventListener('input', updatePaymentSummary);

});
</script>
@endsection

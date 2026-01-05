@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">
<h4 class="fw-bold mb-4">Create Booking</h4>

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

{{-- ================================================= --}}
{{-- ================= TAB 1 : BOOKING =============== --}}
{{-- ================================================= --}}
<div class="step">
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

{{-- BOOKING FORM UPLOAD --}}
<div class="mb-4">
<label class="form-label">Upload Booking Form</label>
<input type="file" name="booking_form_file" class="form-control">
</div>

<hr>

{{-- BOOKING SOURCE --}}
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

{{-- ================================================= --}}
{{-- ================= TAB 2 : APPLICANTS ============= --}}
{{-- ================================================= --}}
<div class="step d-none">
<h5 class="mb-3">Applicants</h5>

@foreach(['primary'=>'Primary Applicant','co'=>'Co Applicant'] as $key=>$label)
<div class="border rounded p-3 mb-4">
<h6>{{ $label }}</h6>

<input type="hidden" name="applicants[{{ $key }}][type]" value="{{ $key }}">

{{-- PERSONAL --}}
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

{{-- KYC --}}
<div class="row mb-3">
<div class="col-md-6"><input name="applicants[{{ $key }}][pan_number]" class="form-control" placeholder="PAN"></div>
<div class="col-md-6"><input name="applicants[{{ $key }}][aadhar_number]" class="form-control" placeholder="Aadhaar"></div>
</div>

<div class="row mb-3">
<div class="col-md-6"><input type="file" name="applicants[{{ $key }}][pan_file]" class="form-control"></div>
<div class="col-md-6"><input type="file" name="applicants[{{ $key }}][aadhar_file]" class="form-control"></div>
</div>

{{-- ADDRESSES --}}
<h6 class="mt-3">Communication Address</h6>
<input name="applicants[{{ $key }}][addresses][communication][address]" class="form-control mb-2" placeholder="Address">
<input name="applicants[{{ $key }}][addresses][communication][city]" class="form-control mb-2" placeholder="City">
<input name="applicants[{{ $key }}][addresses][communication][state]" class="form-control mb-2" placeholder="State">
<input name="applicants[{{ $key }}][addresses][communication][pincode]" class="form-control mb-3" placeholder="Pincode">

<h6>Permanent Address</h6>
<input name="applicants[{{ $key }}][addresses][permanent][address]" class="form-control mb-2" placeholder="Address">
<input name="applicants[{{ $key }}][addresses][permanent][city]" class="form-control mb-2" placeholder="City">
<input name="applicants[{{ $key }}][addresses][permanent][state]" class="form-control mb-2" placeholder="State">
<input name="applicants[{{ $key }}][addresses][permanent][pincode]" class="form-control" placeholder="Pincode">
</div>
@endforeach
</div>

{{-- ================================================= --}}
{{-- ================= TAB 3 : FINANCE ================= --}}
{{-- ================================================= --}}
<div class="step d-none">
    <h5 class="mb-3">Finance</h5>

    {{-- Unit Value --}}
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

    {{-- Agreement Value --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <label class="form-label fw-bold">Agreement Value</label>
            <input type="number" step="0.01" name="finance[agreement_value]" id="agreementValue"
                   class="form-control bg-light" readonly>
        </div>

        <div class="col-md-4">
            <label class="form-label">Current Due (%)</label>
            <input type="number" step="0.01" name="finance[current_due_percent]" class="form-control">
        </div>
    </div>

    <hr>

    {{-- PAYMENTS --}}
    <h6 class="mb-3">Booking Payments</h6>

    <div id="paymentsWrapper">

        {{-- PAYMENT ROW --}}
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
                        <option value="Cash">Cash</option>
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

            {{-- Conditional fields --}}
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

    {{-- ADD PAYMENT --}}
    <button type="button" class="btn btn-outline-primary" id="addPaymentBtn">
        + Add Another Payment
    </button>

    <hr>

    {{-- PAYMENT SUMMARY --}}
    <div class="mt-3">
        <p class="fw-bold mb-1">
            Total Paid:
            <span id="totalPaid">0</span>
        </p>

        <p class="fw-bold">
            Balance Amount:
            <span id="balanceAmount">0</span>
        </p>
    </div>
</div>


{{-- ================================================= --}}
{{-- ========== TAB 5 : CONSENT & SIGNATURES ============ --}}
{{-- ================================================= --}}
<div class="step d-none">
    <h5 class="mb-3">Consent & Signatures</h5>

    {{-- ================= CONSENT DOCUMENTS ================= --}}
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

    {{-- ================= SIGNATURES ================= --}}
    <div class="card">
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
    </div>

    {{-- NOTE --}}
    <p class="text-muted small mt-3">
        All uploaded documents and signatures will be securely stored and
        associated with this booking.
    </p>
</div>

{{-- ================================================= --}}
{{-- ================= TAB 4 : BROKERAGE =============== --}}
{{-- ================================================= --}}
<div class="step d-none">
    <h5 class="mb-3">Brokerage Eligibility</h5>

    {{-- STATUS CARD --}}
    <div class="alert alert-secondary" id="brokerageStatusBox">
        <h6 class="mb-2">
            Eligible for Brokerage:
            <span id="brokerageStatus" class="fw-bold text-danger">
                ❌ Not Eligible
            </span>
        </h6>

        <p class="mb-1" id="brokerageReason">
            Booking does not yet meet brokerage eligibility criteria.
        </p>
    </div>

    {{-- CONDITIONS --}}
    <div class="card border">
        <div class="card-body">
            <h6 class="mb-3">Eligibility Conditions</h6>

            <ul class="mb-0">
                <li>
                    <strong>Scenario 1:</strong><br>
                    Payment completed up to <strong>Threshold %</strong> <br>
                    <em>AND</em> Registration completed
                </li>
                <li class="mt-2">
                    <strong>Scenario 2:</strong><br>
                    Total payment completed up to <strong>Current Due %</strong>
                </li>
            </ul>
        </div>
    </div>

    {{-- PAYMENT SNAPSHOT --}}
    <div class="mt-4">
        <h6>Payment Snapshot</h6>

        <table class="table table-sm table-bordered w-75">
            <tr>
                <th>Total Agreement Value</th>
                <td id="bsAgreementValue">—</td>
            </tr>
            <tr>
                <th>Total Payment Received</th>
                <td id="bsTotalPaid">—</td>
            </tr>
            <tr>
                <th>Payment Completion %</th>
                <td id="bsPaymentPercent">—</td>
            </tr>
            <tr>
                <th>Registration Status</th>
                <td id="bsRegistrationStatus">Pending</td>
            </tr>
        </table>
    </div>

    {{-- NOTE --}}
    <div class="text-muted mt-3 small">
        Brokerage eligibility is auto-calculated based on project configuration
        and payment completion. No manual action required.
    </div>
</div>

{{-- NAV --}}
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
let step = 0;
const steps = document.querySelectorAll('.step');
const tabs = document.querySelectorAll('#stepTabs .nav-link');
const submitStepIndex = 3;

function showStep(){
    steps.forEach((s,i)=>s.classList.toggle('d-none', i !== step));
    tabs.forEach((t,i)=>t.classList.toggle('active', i === step));

    prevBtn.style.display = step === 0 ? 'none' : 'inline-block';
    nextBtn.style.display = step >= submitStepIndex ? 'none' : 'inline-block';

    submitBtn.classList.toggle('d-none', step !== submitStepIndex);
}

nextBtn.onclick=()=>{step++;showStep();}
prevBtn.onclick=()=>{step--;showStep();}
showStep();

/* ---------- SOURCE LOGIC ---------- */

// If any client enquiry selected → hide source
const enquirySelects = document.querySelectorAll('[name^="applicants"][name$="[client_enquiry_id]"]');
const sourceWrapper = document.getElementById('bookingSourceWrapper');

function toggleSourceVisibility(){
    let hasClient = false;
    enquirySelects.forEach(sel=>{
        if(sel.value) hasClient = true;
    });
    sourceWrapper.classList.toggle('d-none', hasClient);
}

enquirySelects.forEach(sel=>{
    sel.addEventListener('change', toggleSourceVisibility);
});

// Source type logic
document.getElementById('bookingSource')?.addEventListener('change', e=>{
    sourceCP.classList.add('d-none');
    sourceReference.classList.add('d-none');
    sourceRemark.classList.add('d-none');

    if(e.target.value === 'Channel Partner') sourceCP.classList.remove('d-none');
    else if(e.target.value === 'Reference') sourceReference.classList.remove('d-none');
    else if(e.target.value) sourceRemark.classList.remove('d-none');
});
</script>
<script>
let paymentIndex = 1;

document.getElementById('addPaymentBtn').addEventListener('click', function () {
    const wrapper = document.getElementById('paymentsWrapper');
    const firstRow = wrapper.querySelector('.payment-row');

    const newRow = firstRow.cloneNode(true);

    // Update all input/select names & reset values
    newRow.querySelectorAll('input, select').forEach(el => {
        if (el.name) {
            el.name = el.name.replace(/\[\d+\]/, `[${paymentIndex}]`);
        }

        if (el.type === 'file' || el.type === 'text' || el.type === 'number' || el.type === 'date') {
            el.value = '';
        }

        if (el.tagName === 'SELECT') {
            el.selectedIndex = 0;
        }
    });

    // Hide conditional fields
    newRow.querySelector('.transaction-field')?.classList.add('d-none');
    newRow.querySelector('.cheque-field')?.classList.add('d-none');
    newRow.querySelector('.cash-proof-field')?.classList.add('d-none');

    wrapper.appendChild(newRow);
    paymentIndex++;
});
</script>

@endsection

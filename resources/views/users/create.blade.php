@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <a href="{{ route('users.index') }}" class="text-muted fw-light">Users</a> / Add User
    </h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Add User</h5>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger mx-3 mt-3">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="accordion" id="userAccordion">

                    <!-- 1️⃣ Basic Information -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingBasic">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasic" aria-expanded="true" aria-controls="collapseBasic">
                                🧩 Basic Information
                            </button>
                        </h2>
                        <div id="collapseBasic" class="accordion-collapse collapse show" aria-labelledby="headingBasic" data-bs-parent="#userAccordion">
                            <div class="accordion-body row">
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" class="form-control">
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">First Name *</label>
                                    <input type="text" name="first_name" class="form-control" >
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="middle_name" class="form-control">
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" name="last_name" class="form-control" >
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Employee Code *</label>
                                    <input type="text" name="employee_code" class="form-control" >
                                    <small class="text-muted">Must be unique</small>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Company *</label>
                                    <select name="entity" class="form-select" >
                                        <option value="">Select Company</option>
                                        @foreach(config('constants.COMPANY_OPTIONS') as $company)
                                            <option value="{{ $company }}">{{ $company }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Business Unit</label>
                                    <select name="business_unit_id" class="form-select">
                                        <option value="">Select Business Unit</option>
                                        @foreach($businessUnits as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-select">
                                        <option value="">Select Gender</option>
                                        @foreach(config('constants.GENDER_OPTIONS') as $key => $gender)
                                            <option value="{{ $key }}">{{ $gender }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Photo</label>
                                    <input type="file" name="photo" class="form-control" accept="image/*">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="Active">Active</option>
                                        <option value="Exited">Exited</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2️⃣ Contact Information -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingContact">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContact" aria-expanded="false" aria-controls="collapseContact">
                                ☎️ Contact Information
                            </button>
                        </h2>
                        <div id="collapseContact" class="accordion-collapse collapse" aria-labelledby="headingContact" data-bs-parent="#userAccordion">
                            <div class="accordion-body row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Official Contact</label>
                                    <input type="text" name="official_contact" class="form-control">
                                    <small class="text-muted">Used for login</small>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Personal Contact</label>
                                    <input type="text" name="personal_contact" class="form-control">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Official Email</label>
                                    <input type="email" name="official_email" class="form-control">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Personal Email</label>
                                    <input type="email" name="personal_email" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3️⃣ Employment Details -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingEmployment">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEmployment" aria-expanded="false" aria-controls="collapseEmployment">
                                🏢 Employment Details
                            </button>
                        </h2>
                        <div id="collapseEmployment" class="accordion-collapse collapse" aria-labelledby="headingEmployment" data-bs-parent="#userAccordion">
                            <div class="accordion-body row">

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Department</label>
                                    <select name="department_id" class="form-select">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Designation</label>
                                    <select name="designation_id" class="form-select">
                                        <option value="">Select Designation</option>
                                        @foreach($designations as $designation)
                                            <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="role_id">Role <span class="text-danger">*</span></label>
                                    <select id="role_id" name="role_id" class="form-select" >
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Reporting Manager</label>
                                    <select name="reporting_manager_id" class="form-select">
                                        <option value="">Select Manager</option>
                                        @foreach($reportingUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->full_name ?? $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Joining Date</label>
                                    <input type="date" id="joining_date" name="joining_date" class="form-control">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Probation Period (Days)</label>
                                    <input type="number" id="probation_period_days" name="probation_period_days" class="form-control">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Confirmation Date</label>
                                    <input type="date" id="confirm_date" name="confirm_date" class="form-control" readonly>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Employment Status</label>
                                    <select id="employment_status" name="employment_status" class="form-select">
                                        <option value="Probation" selected>Probation</option>
                                        <option value="Confirmed">Confirmed</option>
                                    </select>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Leaving Date</label>
                                    <input type="date" name="leaving_date" class="form-control">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Notice Period (Days)</label>
                                    <input type="number" name="notice_period_days" class="form-control">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Location Handled</label>
                                    <input type="text" name="location_handled" class="form-control">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Work Location</label>
                                    <select name="work_location_id" class="form-select">
                                        <option value="">Select Location</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- 4️⃣ Salary & Compensation -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingSalary">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSalary" aria-expanded="false" aria-controls="collapseSalary">
                                💰 Salary & Compensation
                            </button>
                        </h2>
                        <div id="collapseSalary" class="accordion-collapse collapse" aria-labelledby="headingSalary" data-bs-parent="#userAccordion">
                            <div class="accordion-body row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Annual CTC</label>
                                    <input type="number" step="0.01" name="annual_ctc" id="annual_ctc" class="form-control">
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Monthly CTC</label>
                                    <input type="number" step="0.01" name="current_ctc" id="current_ctc" class="form-control bg-light" readonly>
                                </div>

                                @php
                                    $salary_fields = [
                                        'monthly_basic' => 'Monthly Basic',
                                        'monthly_hra' => 'Monthly HRA',
                                        'special_allowance' => 'Special Allowance',
                                        'conveyance_allowance' => 'Conveyance Allowance',
                                        'medical_reimbursement' => 'Medical Reimbursement',
                                        'professional_tax' => 'Professional Tax',
                                        'pf_employer' => 'PF Employer',
                                        'pf_employee' => 'PF Employee',
                                        'net_deductions' => 'Net Deductions',
                                        'net_salary' => 'Net Salary'
                                    ];
                                @endphp

                                @foreach($salary_fields as $field => $label)
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">{{ $label }}</label>
                                        <input type="text" readonly name="{{ $field }}" id="{{ $field }}" class="form-control bg-light">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- 5️⃣ Banking & Statutory -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingBanking">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBanking" aria-expanded="false" aria-controls="collapseBanking">
                                🧾 Statutory & Banking Details
                            </button>
                        </h2>
                        <div id="collapseBanking" class="accordion-collapse collapse" aria-labelledby="headingBanking" data-bs-parent="#userAccordion">
                            <div class="accordion-body row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">PF Status</label>
                                    <select name="pf_status" class="form-select">
                                        <option value="">Select Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">UAN Number</label>
                                    <input type="text" name="uan_number" class="form-control">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Name as per Bank</label>
                                    <input type="text" name="bank_account_name" class="form-control">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Bank Branch Name</label>
                                    <input type="text" name="bank_branch_name" class="form-control">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Account Type</label>
                                    <select name="bank_account_type" class="form-select">
                                        <option value="">Select Account Type</option>
                                        <option value="salary">Salary</option>
                                        <option value="savings">Savings</option>
                                        <option value="current">Current</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Bank Name</label>
                                    <input type="text" name="bank_name" class="form-control">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">IFSC Code</label>
                                    <input type="text" name="ifsc_code" class="form-control">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Bank Account Number</label>
                                    <input type="text" name="bank_account_number" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 6️⃣ Offer & Joining Letters -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingDocuments">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDocuments" aria-expanded="false" aria-controls="collapseDocuments">
                                📄 Offer & Joining Letters
                            </button>
                        </h2>
                        <div id="collapseDocuments" class="accordion-collapse collapse" aria-labelledby="headingDocuments" data-bs-parent="#userAccordion">
                            <div class="accordion-body row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Offer Letter Sent</label>
                                    <select name="offer_letter_sent" class="form-select">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Offer Letter Acknowledged</label>
                                    <select name="offer_letter_acknowledged" class="form-select">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Upload Offer Letter</label>
                                    <input type="file" name="offer_letter_file" class="form-control" accept=".pdf,.doc,.docx">
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Joining Letter Sent</label>
                                    <select name="joining_letter_sent" class="form-select">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Joining Letter Acknowledged</label>
                                    <select name="joining_letter_acknowledged" class="form-select">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Upload Joining Letter</label>
                                    <input type="file" name="joining_letter_file" class="form-control" accept=".pdf,.doc,.docx">
                                </div>

                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Previous Employment Documents</label>
                                    <input type="file" name="previous_documents[]" class="form-control" multiple>
                                    <small class="text-muted">You can upload multiple files (PDF, JPG, PNG).</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 7️⃣ Personal & Emergency -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingPersonal">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePersonal" aria-expanded="false" aria-controls="collapsePersonal">
                                🏠 Personal & Emergency Info
                            </button>
                        </h2>
                        <div id="collapsePersonal" class="accordion-collapse collapse" aria-labelledby="headingPersonal" data-bs-parent="#userAccordion">
                            <div class="accordion-body row">
                                @php
                                    $personal_fields = [
                                        'dob'=>'date','blood_group'=>'text','communication_address'=>'textarea',
                                        'permanent_address'=>'textarea','languages_known'=>'text','education_qualification'=>'text','marital_status'=>'text','marriage_date'=>'date',
                                        'spouse_name'=>'text','parents_contact'=>'text','emergency_contact_name'=>'text','emergency_contact_relationship'=>'text','emergency_contact_number'=>'text',
                                        'pan_no'=>'text','aadhar_no'=>'text'
                                    ];
                                @endphp
                                @foreach($personal_fields as $field => $type)
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">{{ ucwords(str_replace('_',' ',$field)) }}</label>
                                        @if($type == 'textarea')
                                            <textarea name="{{ $field }}" class="form-control"></textarea>
                                        @else
                                            <input type="{{ $type }}" name="{{ $field }}" class="form-control">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- 8️⃣ Assets & Misc -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingAssets">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAssets" aria-expanded="false" aria-controls="collapseAssets">
                                💻 Company Assets & Miscellaneous
                            </button>
                        </h2>
                        <div id="collapseAssets" class="accordion-collapse collapse" aria-labelledby="headingAssets" data-bs-parent="#userAccordion">
                            <div class="accordion-body row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Work Off</label>
                                    <input type="text" name="work_off" class="form-control">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Additional Comments</label>
                                    <input type="text" name="additional_comments" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script>
   document.addEventListener('DOMContentLoaded', function () {

    const joiningDate = document.getElementById('joining_date');
    const probationDays = document.getElementById('probation_period_days');
    const confirmDate = document.getElementById('confirm_date');

    function calculateConfirmDate() {
        if (!joiningDate.value || !probationDays.value) return;

        const join = new Date(joiningDate.value);
        join.setDate(join.getDate() + parseInt(probationDays.value));

        const yyyy = join.getFullYear();
        const mm = String(join.getMonth() + 1).padStart(2, '0');
        const dd = String(join.getDate()).padStart(2, '0');

        confirmDate.value = `${yyyy}-${mm}-${dd}`;
    }

    probationDays.addEventListener('input', calculateConfirmDate);
    joiningDate.addEventListener('change', calculateConfirmDate);

    // auto-calc on edit load
    calculateConfirmDate();
});

</script>

<script>

    // Annual → Monthly CTC
    document.getElementById('annual_ctc')?.addEventListener('input', function () {
        const annual = parseFloat(this.value || 0);
        if (annual <= 0) return;
        const monthly = annual / 12;
        document.getElementById('current_ctc').value = monthly.toFixed(2);

        const basic = monthly * 0.50;
        const hra = basic * 0.50;
        const special = monthly * 0.10;
        const convey = monthly * 0.10;
        const medical = monthly * 0.05;

        const pfEmployer = 1800;
        const pfEmployee = 1800;
        const profTax = 200;

        const deductions = (pfEmployee + pfEmployer) + profTax;
        const net = monthly - deductions;

        document.getElementById('monthly_basic').value = basic.toFixed(2);
        document.getElementById('monthly_hra').value = hra.toFixed(2);
        document.getElementById('special_allowance').value = special.toFixed(2);
        document.getElementById('conveyance_allowance').value = convey.toFixed(2);
        document.getElementById('medical_reimbursement').value = medical.toFixed(2);
        document.getElementById('professional_tax').value = profTax.toFixed(2);
        document.getElementById('pf_employer').value = pfEmployer.toFixed(2);
        document.getElementById('pf_employee').value = pfEmployee.toFixed(2);
        document.getElementById('net_deductions').value = deductions.toFixed(2);
        document.getElementById('net_salary').value = net.toFixed(2);
    });
</script>
@endsection

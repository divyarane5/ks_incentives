@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <a href="{{ route('users.index') }}" class="text-muted fw-light">Users</a> / Edit User
    </h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> Please fix the following errors:<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Edit User</h5>
        </div>

        <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="accordion" id="userAccordion">

                    <!-- 1️⃣ Basic Information -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingBasic">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasic" aria-expanded="true">
                                🧩 Basic Information
                            </button>
                        </h2>
                        <div id="collapseBasic" class="accordion-collapse collapse show" data-bs-parent="#userAccordion">
                            <div class="accordion-body row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Employee Code *</label>
                                    <input type="text" name="employee_code" value="{{ old('employee_code', $user->employee_code) }}" class="form-control @error('employee_code') is-invalid @enderror" required>
                                    @error('employee_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" value="{{ old('title', $user->title) }}" class="form-control @error('title') is-invalid @enderror">
                                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">First Name *</label>
                                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="form-control @error('first_name') is-invalid @enderror" required>
                                    @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}" class="form-control @error('middle_name') is-invalid @enderror">
                                    @error('middle_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="form-control @error('last_name') is-invalid @enderror" required>
                                    @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Company *</label>
                                    <select name="entity" class="form-select @error('entity') is-invalid @enderror" required>
                                        <option value="">Select Company</option>
                                        @foreach(config('constants.COMPANY_OPTIONS') as $company)
                                            <option value="{{ $company }}" {{ old('entity', $user->entity) == $company ? 'selected' : '' }}>{{ $company }}</option>
                                        @endforeach
                                    </select>
                                    @error('entity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Business Unit</label>
                                    <select name="business_unit_id" id="business_unit_id"
                                            class="form-select @error('business_unit_id') is-invalid @enderror">
                                        <option value="">Select Business Unit</option>

                                        @foreach($businessUnits as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ old('business_unit_id', $user->business_unit_id) == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach

                                    </select>
                                    @error('business_unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                        <option value="">Select Gender</option>
                                        @foreach(config('constants.GENDER_OPTIONS') as $key => $gender)
                                            <option value="{{ $key }}" {{ old('gender', $user->gender) == $key ? 'selected' : '' }}>{{ $gender }}</option>
                                        @endforeach
                                    </select>
                                    @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Photo</label>
                                    @if($user->photo)
                                        <img src="{{ asset('storage/'.$user->photo) }}" width="100" class="mb-2"><br>
                                    @endif
                                    <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                                    @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                                        <option value="Active" {{ old('status', $user->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Onboarding" {{ old('status', $user->status) == 'Onboarding' ? 'selected' : '' }}>Onboarding</option>
                                        <option value="Exited" {{ old('status', $user->status) == 'Exited' ? 'selected' : '' }}>Exited</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2️⃣ Contact Information -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingContact">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContact" aria-expanded="false">
                                ☎️ Contact Information
                            </button>
                        </h2>
                        <div id="collapseContact" class="accordion-collapse collapse" data-bs-parent="#userAccordion">
                            <div class="accordion-body row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Official Contact</label>
                                    <input type="text" name="official_contact" value="{{ old('official_contact', $user->official_contact) }}" class="form-control @error('official_contact') is-invalid @enderror">
                                    @error('official_contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Personal Contact</label>
                                    <input type="text" name="personal_contact" value="{{ old('personal_contact', $user->personal_contact) }}" class="form-control @error('personal_contact') is-invalid @enderror">
                                    @error('personal_contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Official Email</label>
                                    <input type="email" name="official_email" value="{{ old('official_email', $user->official_email) }}" class="form-control @error('official_email') is-invalid @enderror">
                                    @error('official_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Personal Email</label>
                                    <input type="email" name="personal_email" value="{{ old('personal_email', $user->personal_email) }}" class="form-control @error('personal_email') is-invalid @enderror">
                                    @error('personal_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3️⃣ Employment Details (complete) -->
                    <!-- 3️⃣ Employment Details (EDIT – COMPLETE & FIXED) -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingEmployment">
                            <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseEmployment"
                                aria-expanded="false" aria-controls="collapseEmployment">
                                🏢 Employment Details
                            </button>
                        </h2>

                        <div id="collapseEmployment" class="accordion-collapse collapse"
                            aria-labelledby="headingEmployment" data-bs-parent="#userAccordion">

                            <div class="accordion-body row g-3">

                                <!-- Department -->
                                <div class="col-md-6">
                                    <label class="form-label">Department</label>
                                    <select name="department_id" class="form-select">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Designation -->
                                <div class="col-md-6">
                                    <label class="form-label">Designation</label>
                                    <select name="designation_id" class="form-select">
                                        <option value="">Select Designation</option>
                                        @foreach($designations as $designation)
                                            <option value="{{ $designation->id }}"
                                                {{ old('designation_id', $user->designation_id) == $designation->id ? 'selected' : '' }}>
                                                {{ $designation->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Role -->
                                <div class="col-md-6">
                                    <label class="form-label">Role <span class="text-danger">*</span></label>
                                    <select name="role_id" class="form-select" required>
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Reporting Manager -->
                                <div class="col-md-6">
                                    <label class="form-label">Reporting Manager</label>
                                    <select name="reporting_manager_id" class="form-select">
                                        <option value="">Select Manager</option>
                                        @foreach($reportingUsers as $mgr)
                                            <option value="{{ $mgr->id }}"
                                                {{ old('reporting_manager_id', $user->reporting_manager_id) == $mgr->id ? 'selected' : '' }}>
                                                {{ $mgr->full_name ?? $mgr->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Joining Date -->
                                <div class="col-md-6">
                                    <label class="form-label">Joining Date</label>
                                    <input type="date"
                                        name="joining_date"
                                        id="joining_date"
                                        value="{{ old('joining_date', $user->joining_date) }}"
                                        class="form-control">
                                </div>

                                <!-- Probation Period -->
                                <div class="col-md-6">
                                    <label class="form-label">Probation Period (Days)</label>
                                    <input type="number"
                                        name="probation_period_days"
                                        id="probation_period_days"
                                        value="{{ old('probation_period_days', $user->probation_period_days) }}"
                                        class="form-control">
                                </div>

                                <!-- Confirmation Date -->
                                <div class="col-md-6">
                                    <label class="form-label">Confirmation Date</label>
                                    <input type="date"
                                        name="confirm_date"
                                        id="confirm_date"
                                        value="{{ old('confirm_date', $user->confirm_date) }}"
                                        class="form-control"
                                        readonly>
                                </div>

                                <!-- Employment Status -->
                                <div class="col-md-6">
                                    <label class="form-label">Employment Status</label>
                                    <select name="employment_status" class="form-select">
                                        <option value="Probation" {{ old('employment_status', $user->employment_status) == 'Probation' ? 'selected' : '' }}>Probation</option>
                                        <option value="Confirmed" {{ old('employment_status', $user->employment_status) == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    </select>
                                </div>

                                <!-- Leaving Date -->
                                <div class="col-md-6">
                                    <label class="form-label">Leaving Date</label>
                                    <input type="date" name="leaving_date"
                                        class="form-control"
                                        value="{{ old('leaving_date', $user->leaving_date ?? $user->latestExitHistory?->exit_date) }}">
                                </div>

                                <!-- Notice Period -->
                                <div class="col-md-6">
                                    <label class="form-label">Notice Period (Days)</label>
                                    <input type="number" name="notice_period_days"
                                        class="form-control"
                                        value="{{ old('notice_period_days', $user->notice_period_days) }}">
                                </div>

                                <!-- Exit Status -->
                                <div class="col-md-6">
                                    <label class="form-label">Exit Status</label>
                                    <select name="exit_status" class="form-select">
                                        <option value="">Select Exit Status</option>
                                        @foreach(['Resigned','Terminated','Absconded','Retired'] as $status)
                                            <option value="{{ $status }}"
                                                {{ old('exit_status', $user->exit_status ?? $user->latestExitHistory?->exit_type) == $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Reason for Leaving -->
                                <div class="col-md-6">
                                    <label class="form-label">Reason for Leaving</label>
                                    <input type="text" name="reason_for_leaving"
                                        class="form-control"
                                        value="{{ old('reason_for_leaving', $user->reason_for_leaving ?? $user->latestExitHistory?->exit_reason) }}">

                                </div>

                                <!-- Location Handled -->
                                <div class="col-md-6">
                                    <label class="form-label">Location Handled</label>
                                    <input type="text" name="location_handled"
                                        value="{{ old('location_handled', $user->location_handled) }}"
                                        class="form-control">
                                </div>

                                <!-- Work Location -->
                                <div class="col-md-6">
                                    <label class="form-label">Work Location</label>
                                    <select name="work_location_id" class="form-select">
                                        <option value="">Select Location</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}"
                                                {{ old('work_location_id', $user->work_location_id) == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>



                    <!-- 4️⃣ Salary & Compensation (Edit) -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingSalary">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapseSalary"
                                    aria-expanded="false">
                                💰 Salary & Compensation
                            </button>
                        </h2>

                        <div id="collapseSalary" class="accordion-collapse collapse"
                            data-bs-parent="#userAccordion">
                            <div class="accordion-body row g-3">

                                <!-- Annual CTC -->
                                <div class="col-md-4">
                                    <label class="form-label">Annual CTC</label>
                                    <input type="number" step="0.01"
                                        name="annual_ctc"
                                        id="annual_ctc"
                                        value="{{ old('annual_ctc', $user->annual_ctc) }}"
                                        class="form-control">
                                </div>

                                <!-- Monthly CTC -->
                                <div class="col-md-4">
                                    <label class="form-label">Monthly CTC</label>
                                    <input type="number" step="0.01"
                                        name="current_ctc"
                                        id="current_ctc"
                                        value="{{ old('current_ctc', $user->current_ctc) }}"
                                        class="form-control bg-light"
                                        readonly>
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
                                        'net_salary' => 'Net Salary',
                                    ];
                                @endphp

                                @foreach($salary_fields as $field => $label)
                                    <div class="col-md-4">
                                        <label class="form-label">{{ $label }}</label>
                                        <input type="text"
                                            name="{{ $field }}"
                                            id="{{ $field }}"
                                            value="{{ old($field, $user->$field) }}"
                                            class="form-control bg-light"
                                            readonly>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>


                    <!-- 6️⃣ Statutory & Banking (Edit) -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingBanking">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapseBanking"
                                    aria-expanded="false"
                                    aria-controls="collapseBanking">
                                🧾 Statutory & Banking Details
                            </button>
                        </h2>

                        <div id="collapseBanking" class="accordion-collapse collapse"
                            aria-labelledby="headingBanking"
                            data-bs-parent="#userAccordion">
                            <div class="accordion-body row g-3">

                                <!-- PF Status -->
                                <div class="col-md-6">
                                    <label class="form-label">PF Status</label>
                                    <select name="pf_status" id="pf_status"
                                            class="form-select @error('pf_status') is-invalid @enderror">
                                        <option value="">Select Status</option>
                                        <option value="1" {{ old('pf_status', $user->pf_status) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('pf_status', $user->pf_status) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('pf_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- UAN Number -->
                                <div class="col-md-6">
                                    <label class="form-label">UAN Number</label>
                                    <input type="text"
                                        name="uan_number"
                                        value="{{ old('uan_number', $user->uan_number) }}"
                                        class="form-control @error('uan_number') is-invalid @enderror">
                                    @error('uan_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Name as per Bank -->
                                <div class="col-md-6">
                                    <label class="form-label">Name as per Bank</label>
                                    <input type="text"
                                        name="bank_account_name"
                                        value="{{ old('bank_account_name', $user->bank_account_name) }}"
                                        class="form-control @error('bank_account_name') is-invalid @enderror">
                                    @error('bank_account_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Bank Branch Name -->
                                <div class="col-md-6">
                                    <label class="form-label">Bank Branch Name</label>
                                    <input type="text"
                                        name="bank_branch_name"
                                        value="{{ old('bank_branch_name', $user->bank_branch_name) }}"
                                        class="form-control @error('bank_branch_name') is-invalid @enderror">
                                    @error('bank_branch_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Account Type -->
                                <div class="col-md-6">
                                    <label class="form-label">Account Type</label>
                                    <select name="bank_account_type"
                                            class="form-select @error('bank_account_type') is-invalid @enderror">
                                        <option value="">Select Account Type</option>
                                        <option value="salary" {{ old('bank_account_type', $user->bank_account_type) == 'salary' ? 'selected' : '' }}>Salary</option>
                                        <option value="savings" {{ old('bank_account_type', $user->bank_account_type) == 'savings' ? 'selected' : '' }}>Savings</option>
                                        <option value="current" {{ old('bank_account_type', $user->bank_account_type) == 'current' ? 'selected' : '' }}>Current</option>
                                    </select>
                                    @error('bank_account_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Bank Name -->
                                <div class="col-md-6">
                                    <label class="form-label">Bank Name</label>
                                    <input type="text"
                                        name="bank_name"
                                        value="{{ old('bank_name', $user->bank_name) }}"
                                        class="form-control @error('bank_name') is-invalid @enderror">
                                    @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- IFSC Code -->
                                <div class="col-md-6">
                                    <label class="form-label">IFSC Code</label>
                                    <input type="text"
                                        name="ifsc_code"
                                        value="{{ old('ifsc_code', $user->ifsc_code) }}"
                                        class="form-control @error('ifsc_code') is-invalid @enderror">
                                    @error('ifsc_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Bank Account Number -->
                                <div class="col-md-6">
                                    <label class="form-label">Bank Account Number</label>
                                    <input type="text"
                                        name="bank_account_number"
                                        value="{{ old('bank_account_number', $user->bank_account_number) }}"
                                        class="form-control @error('bank_account_number') is-invalid @enderror">
                                    @error('bank_account_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- 6️⃣ Offer & Joining Letters -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingDocuments">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseDocuments" aria-expanded="false">
                                📄 Offer & Joining Letters
                            </button>
                        </h2>

                        <div id="collapseDocuments" class="accordion-collapse collapse" data-bs-parent="#userAccordion">
                            <div class="accordion-body row">

                                {{-- Offer Letter --}}
                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Offer Letter Sent</label>
                                    <select name="offer_letter_sent" class="form-select">
                                        <option value="0" {{ old('offer_letter_sent', $user->offer_letter_sent) == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ old('offer_letter_sent', $user->offer_letter_sent) == 1 ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Offer Letter Acknowledged</label>
                                    <select name="offer_letter_acknowledged" class="form-select">
                                        <option value="0" {{ old('offer_letter_acknowledged', $user->offer_letter_acknowledged) == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ old('offer_letter_acknowledged', $user->offer_letter_acknowledged) == 1 ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Upload Offer Letter</label>
                                    <input type="file" name="offer_letter_file" class="form-control" accept=".pdf,.doc,.docx">
                                    @if($user->offer_letter_file)
                                        <small class="d-block mt-1">
                                            📎 <a href="{{ asset('storage/'.$user->offer_letter_file) }}" target="_blank">
                                                View Offer Letter
                                            </a>
                                        </small>
                                    @endif
                                </div>

                                {{-- Joining Letter --}}
                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Joining Letter Sent</label>
                                    <select name="joining_letter_sent" class="form-select">
                                        <option value="0" {{ old('joining_letter_sent', $user->joining_letter_sent) == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ old('joining_letter_sent', $user->joining_letter_sent) == 1 ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Joining Letter Acknowledged</label>
                                    <select name="joining_letter_acknowledged" class="form-select">
                                        <option value="0" {{ old('joining_letter_acknowledged', $user->joining_letter_acknowledged) == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ old('joining_letter_acknowledged', $user->joining_letter_acknowledged) == 1 ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Upload Joining Letter</label>
                                    <input type="file" name="joining_letter_file" class="form-control" accept=".pdf,.doc,.docx">
                                    @if($user->joining_letter_file)
                                        <small class="d-block mt-1">
                                            📎 <a href="{{ asset('storage/'.$user->joining_letter_file) }}" target="_blank">
                                                View Joining Letter
                                            </a>
                                        </small>
                                    @endif
                                </div>

                                {{-- Previous Employment Documents --}}
                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Previous Employment Documents</label>
                                    <input type="file" name="previous_documents[]" class="form-control" multiple>

                                    @if($user->previousEmploymentDocuments->count())
                                        <div class="mt-2">
                                            <strong>Uploaded Documents:</strong>
                                            <ul class="mb-0">
                                                @foreach($user->previousEmploymentDocuments as $doc)
                                                    <li>
                                                        📎 <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank">
                                                            {{ basename($doc->file_path) }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <small class="text-muted d-block mt-1">
                                            No previous employment documents uploaded.
                                        </small>
                                    @endif
                                </div>


                            </div>
                        </div>
                    </div>




                    <!-- 7️⃣ Personal & Emergency -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingPersonal">
                            <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapsePersonal"
                                aria-expanded="false"
                                aria-controls="collapsePersonal">
                                🏠 Personal & Emergency Info
                            </button>
                        </h2>

                        <div id="collapsePersonal"
                            class="accordion-collapse collapse"
                            aria-labelledby="headingPersonal"
                            data-bs-parent="#userAccordion">

                            <div class="accordion-body row">
                                @php
                                    $personal_fields = [
                                        'dob' => 'date',
                                        'blood_group' => 'text',
                                        'communication_address' => 'textarea',
                                        'permanent_address' => 'textarea',
                                        'languages_known' => 'text',
                                        'education_qualification' => 'text',
                                        'marital_status' => 'text',
                                        'marriage_date' => 'date',
                                        'spouse_name' => 'text',
                                        'parents_contact' => 'text',
                                        'emergency_contact_name' => 'text',
                                        'emergency_contact_relationship' => 'text',
                                        'emergency_contact_number' => 'text',
                                        'pan_no' => 'text',
                                        'aadhar_no' => 'text'
                                    ];
                                @endphp

                                @foreach($personal_fields as $field => $type)
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">
                                            {{ ucwords(str_replace('_',' ',$field)) }}
                                        </label>

                                        @if($type === 'textarea')
                                            <textarea name="{{ $field }}" class="form-control"
                                                rows="2">{{ old($field, $user->$field ?? '') }}</textarea>
                                        @else
                                            <input type="{{ $type }}"
                                                name="{{ $field }}"
                                                value="{{ old($field, $user->$field ?? '') }}"
                                                class="form-control">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- 8️⃣ Assets -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingAssets">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseAssets" aria-expanded="false" aria-controls="collapseAssets">
                                💻 Company Assets & Miscellaneous
                            </button>
                        </h2>
                        <div id="collapseAssets" class="accordion-collapse collapse" aria-labelledby="headingAssets"
                            data-bs-parent="#userAccordion">
                            <div class="accordion-body row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Work Off</label>
                                    <input type="text" name="work_off" value="{{ old('work_off', $user->work_off) }}" class="form-control">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Additional Comments</label>
                                    <input type="text" name="additional_comments" value="{{ old('additional_comments', $user->additional_comments) }}" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
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

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
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingEmployment">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseEmployment" aria-expanded="false" aria-controls="collapseEmployment">
                                🏢 Employment Details
                            </button>
                        </h2>

                        <div id="collapseEmployment" class="accordion-collapse collapse" aria-labelledby="headingEmployment" data-bs-parent="#userAccordion">
                            <div class="accordion-body row g-3">

                                <!-- Department -->
                                <div class="col-md-6 mb-3">
                                    <label for="department_id" class="form-label">Department</label>
                                    <select id="department_id" name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Designation -->
                                <div class="col-md-6 mb-3">
                                    <label for="designation_id" class="form-label">Designation</label>
                                    <select id="designation_id" name="designation_id" class="form-select @error('designation_id') is-invalid @enderror">
                                        <option value="">Select Designation</option>
                                        @foreach($designations as $designation)
                                            <option value="{{ $designation->id }}" {{ old('designation_id', $user->designation_id) == $designation->id ? 'selected' : '' }}>
                                                {{ $designation->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('designation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Role -->
                                <div class="col-md-6 mb-3">
                                    <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select id="role_id" name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Reporting Manager -->
                                <div class="col-md-6 mb-3">
                                    <label for="reporting_manager_id" class="form-label">Reporting Manager</label>
                                    <select id="reporting_manager_id" name="reporting_manager_id" class="form-select @error('reporting_manager_id') is-invalid @enderror">
                                        <option value="">Select Manager</option>
                                        @foreach($reportingUsers as $mgr)
                                            <option value="{{ $mgr->id }}" {{ old('reporting_manager_id', $user->reporting_manager_id) == $mgr->id ? 'selected' : '' }}>
                                                {{ $mgr->full_name ?? $mgr->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('reporting_manager_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Location Handled -->
                                <div class="col-md-6 mb-3">
                                    <label for="location_handled" class="form-label">Location Handled</label>
                                    <input id="location_handled" type="text" name="location_handled" value="{{ old('location_handled', $user->location_handled) }}" class="form-control @error('location_handled') is-invalid @enderror">
                                    @error('location_handled') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Work Location -->
                                <div class="col-md-6 mb-3">
                                    <label for="work_location_id" class="form-label">Work Location</label>
                                    <select id="work_location_id" name="work_location_id" class="form-select @error('work_location_id') is-invalid @enderror">
                                        <option value="">Select Location</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('work_location_id', $user->work_location_id) == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('work_location_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Joining Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="joining_date" class="form-label">Joining Date</label>
                                    <input id="joining_date" type="date" name="joining_date" value="{{ old('joining_date', optional($user->joining_date)->format('Y-m-d') ?? $user->joining_date) }}" class="form-control @error('joining_date') is-invalid @enderror">
                                    @error('joining_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Confirmation Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_date" class="form-label">Confirmation Date</label>
                                    <input id="confirm_date" type="date" name="confirm_date" value="{{ old('confirm_date', optional($user->confirm_date)->format('Y-m-d') ?? $user->confirm_date) }}" class="form-control @error('confirm_date') is-invalid @enderror">
                                    @error('confirm_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Leaving Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="leaving_date" class="form-label">Leaving Date</label>
                                    <input id="leaving_date" type="date" name="leaving_date" value="{{ old('leaving_date', optional($user->leaving_date)->format('Y-m-d') ?? $user->leaving_date) }}" class="form-control @error('leaving_date') is-invalid @enderror">
                                    @error('leaving_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Exit Status -->
                                <div class="col-md-6 mb-3">
                                    <label for="exit_status" class="form-label">Exit Status</label>
                                    <select id="exit_status" name="exit_status" class="form-select @error('exit_status') is-invalid @enderror">
                                        <option value="">Select Exit Status</option>
                                        @foreach(['Resigned','Terminated','Absconded','Retired'] as $status)
                                            <option value="{{ $status }}" {{ old('exit_status', $user->exit_status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    @error('exit_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Reason for Leaving -->
                                <div class="col-md-6 mb-3">
                                    <label for="reason_for_leaving" class="form-label">Reason for Leaving</label>
                                    <input id="reason_for_leaving" type="text" name="reason_for_leaving" value="{{ old('reason_for_leaving', $user->reason_for_leaving) }}" class="form-control @error('reason_for_leaving') is-invalid @enderror">
                                    @error('reason_for_leaving') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- FNF Status -->
                                <div class="col-md-6 mb-3">
                                    <label for="fnf_status" class="form-label">FNF Status</label>
                                    <select id="fnf_status" name="fnf_status" class="form-select @error('fnf_status') is-invalid @enderror">
                                        <option value="">Select FNF Status</option>
                                        @foreach(['Pending','In Progress','Completed'] as $fnf)
                                            <option value="{{ $fnf }}" {{ old('fnf_status', $user->fnf_status) == $fnf ? 'selected' : '' }}>{{ $fnf }}</option>
                                        @endforeach
                                    </select>
                                    @error('fnf_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                            </div>
                        </div>
                    </div>


                    <!-- 4️⃣ Salary Information -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingSalary">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSalary">
                                💰 Salary Information
                            </button>
                        </h2>
                        <div id="collapseSalary" class="accordion-collapse collapse" data-bs-parent="#userAccordion">
                            <div class="accordion-body row">
                                @php
                                    $salaryFields = [
                                        'current_ctc', 'monthly_basic', 'monthly_hra', 'special_allowance',
                                        'conveyance_allowance', 'medical_reimbursement', 'professional_tax',
                                        'pf_employer', 'pf_employee', 'net_deductions', 'net_salary'
                                    ];
                                @endphp
                                @foreach($salaryFields as $field)
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">{{ ucwords(str_replace('_',' ',$field)) }}</label>
                                        <input type="{{ $field == 'current_ctc' ? 'number' : 'text' }}" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $user->$field) }}" class="form-control" {{ $field != 'current_ctc' ? 'readonly' : '' }}>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- 6️⃣ Statutory & Banking -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingBanking">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseBanking" aria-expanded="false" aria-controls="collapseBanking">
                                🧾 Statutory & Banking Details
                            </button>
                        </h2>
                        <div id="collapseBanking" class="accordion-collapse collapse" aria-labelledby="headingBanking"
                            data-bs-parent="#userAccordion">
                            <div class="accordion-body row">

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">PF Status</label>
                                    <select name="pf_status" class="form-select @error('pf_status') is-invalid @enderror">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ old('pf_status', $user->pf_status) == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('pf_status', $user->pf_status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('pf_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">UAN Number</label>
                                    <input type="text" name="uan_number" value="{{ old('uan_number', $user->uan_number) }}"
                                        class="form-control @error('uan_number') is-invalid @enderror">
                                    @error('uan_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Bank Name</label>
                                    <input type="text" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}"
                                        class="form-control @error('bank_name') is-invalid @enderror">
                                    @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">IFSC Code</label>
                                    <input type="text" name="ifsc_code" value="{{ old('ifsc_code', $user->ifsc_code) }}"
                                        class="form-control @error('ifsc_code') is-invalid @enderror">
                                    @error('ifsc_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Bank Account Number</label>
                                    <input type="text" name="bank_account_number"
                                        value="{{ old('bank_account_number', $user->bank_account_number) }}"
                                        class="form-control @error('bank_account_number') is-invalid @enderror">
                                    @error('bank_account_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                            </div>
                        </div>
                    </div>


                    <!-- 7️⃣ Personal -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingPersonal">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapsePersonal" aria-expanded="false" aria-controls="collapsePersonal">
                                🏠 Personal & Emergency Info
                            </button>
                        </h2>
                        <div id="collapsePersonal" class="accordion-collapse collapse" aria-labelledby="headingPersonal"
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
                                        <label class="form-label">{{ ucwords(str_replace('_',' ',$field)) }}</label>
                                        @if($type == 'textarea')
                                            <textarea name="{{ $field }}" class="form-control">{{ old($field, $user->$field) }}</textarea>
                                        @else
                                            <input type="{{ $type }}" name="{{ $field }}" value="{{ old($field, $user->$field) }}" class="form-control">
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
    // Salary calculation
    const ctcInput = document.getElementById('current_ctc');
    if(ctcInput){
        ctcInput.dispatchEvent(new Event('input'));
        ctcInput.addEventListener('input', function () {
            const ctc = parseFloat(this.value || 0);
            const monthly = ctc/12;
            const basic = monthly*0.5;
            const hra = basic*0.5;
            const special = monthly*0.1;
            const convey = monthly*0.1;
            const medical = monthly*0.05;
            const pfEmployer = 1800;
            const pfEmployee = 1800;
            const profTax = 200;
            const deductions = pfEmployee + pfEmployer + profTax;
            const net = monthly - deductions;

            const fields = ['monthly_basic','monthly_hra','special_allowance','conveyance_allowance','medical_reimbursement','professional_tax','pf_employer','pf_employee','net_deductions','net_salary'];
            const values = [basic, hra, special, convey, medical, profTax, pfEmployer, pfEmployee, deductions, net];
            fields.forEach((field, i) => {
                if(document.getElementById(field)) document.getElementById(field).value = values[i].toFixed(2);
            });
        });
    }
</script>
@endsection

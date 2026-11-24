@use('App\Support\PeopleFormat', 'F')
@extends('layouts.app')

@section('content')
@include('users.partials.people-styles')
@php
    $v = fn ($field, $default = null) => old($field, $user->$field ?? $default);
    $dateVal = function ($field) use ($user) {
        $val = old($field, $user->$field);
        return $val ? \Carbon\Carbon::parse($val)->format('Y-m-d') : '';
    };
    $err = fn ($field) => $errors->has($field) ? 'is-invalid' : '';
    $sections = [
        'basic' => 'Basic', 'contact' => 'Contact', 'job' => 'Job', 'pay' => 'Pay',
        'bank' => 'Bank & IDs', 'documents' => 'Documents', 'personal' => 'Personal', 'assets' => 'Assets',
    ];
@endphp

<div class="container-xxl flex-grow-1 container-p-y kp kp-form">
    <div class="kp-crumb">
        <a href="{{ route('users.index') }}">KeyPeople</a> <i class="bx bx-chevron-right"></i>
        <a href="{{ route('users.show', $user->id) }}">{{ $user->first_name ?: $user->name }}</a> <i class="bx bx-chevron-right"></i>
        <span>Edit</span>
    </div>

    <div class="d-flex align-items-center gap-3 mb-3">
        @if($user->photo)
            <img src="{{ asset('storage/'.$user->photo) }}" alt="" style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:2px solid var(--kp-gold)">
        @else
            <div style="width:52px;height:52px;border-radius:50%;background:var(--kp-navy);color:#fff;display:grid;place-items:center;font-weight:700">{{ F::initials($user->name) }}</div>
        @endif
        <div style="min-width:0">
            <h4 class="mb-0">Edit {{ trim($user->first_name.' '.$user->last_name) ?: $user->name }}</h4>
            <div class="text-muted small">{{ $user->employee_code }} · {{ $user->designation?->name ?? 'No designation' }}</div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" id="kpErrors">
            <strong>{{ $errors->count() }} {{ \Illuminate\Support\Str::plural('thing', $errors->count()) }} to fix before saving:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <nav class="kp-jump" id="kpJump">
        @foreach($sections as $id => $label)
            <a href="#sec-{{ $id }}" data-sec="sec-{{ $id }}">{{ $label }}</a>
        @endforeach
    </nav>

    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" id="userForm" novalidate>
        @csrf
        @method('PUT')

        {{-- BASIC --}}
        <section class="kp-panel kp-section" id="sec-basic">
            <div class="kp-panel-h"><h2><i class="bx bx-id-card"></i>Basic details</h2><span class="kp-note">* required</span></div>
            <div class="kp-panel-b row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label">Employee code *</label>
                    <input type="text" name="employee_code" value="{{ $v('employee_code') }}" class="form-control {{ $err('employee_code') }}" required>
                    @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-4 col-md-2">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" value="{{ $v('title') }}" class="form-control {{ $err('title') }}" list="titleList" placeholder="Mr / Ms">
                    <datalist id="titleList"><option value="Mr."><option value="Ms."><option value="Mrs."><option value="Dr."></datalist>
                </div>
                <div class="col-8 col-md-6">
                    <label class="form-label">First name *</label>
                    <input type="text" name="first_name" value="{{ $v('first_name') }}" class="form-control {{ $err('first_name') }}" required autocomplete="off">
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Middle name</label>
                    <input type="text" name="middle_name" value="{{ $v('middle_name') }}" class="form-control {{ $err('middle_name') }}" autocomplete="off">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Last name *</label>
                    <input type="text" name="last_name" value="{{ $v('last_name') }}" class="form-control {{ $err('last_name') }}" required autocomplete="off">
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label">Gender</label>
                    <select id="gender" name="gender" class="form-select raw-select {{ $err('gender') }}">
                        <option value="">Select</option>
                        @foreach(config('constants.GENDER_OPTIONS') as $key => $gender)
                            <option value="{{ $key }}" {{ $v('gender') == $key ? 'selected' : '' }}>{{ $gender }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label">Date of birth</label>
                    <input type="date" name="dob" value="{{ $dateVal('dob') }}" class="form-control {{ $err('dob') }}">
                    @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select raw-select {{ $err('status') }}">
                        <option value="Active" {{ $v('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Exited" {{ $v('status') == 'Exited' ? 'selected' : '' }}>Exited</option>
                        @if(! in_array($v('status'), ['Active', 'Exited'], true) && $v('status'))
                            <option value="{{ $v('status') }}" selected>{{ $v('status') }}</option>
                        @endif
                    </select>
                    <div class="form-text">Exited people can't sign in and their visiting card stops working.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Company *</label>
                    <select name="entity" class="form-select raw-select {{ $err('entity') }}" required>
                        <option value="">Select company</option>
                        @foreach(config('constants.COMPANY_OPTIONS') as $company)
                            <option value="{{ $company }}" {{ $v('entity') == $company ? 'selected' : '' }}>{{ $company }}</option>
                        @endforeach
                    </select>
                    @error('entity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Business unit</label>
                    <select name="business_unit_id" id="business_unit_id" class="form-select raw-select {{ $err('business_unit_id') }}">
                        <option value="">Select business unit</option>
                        @foreach($businessUnits as $id => $name)
                            <option value="{{ $id }}" {{ $v('business_unit_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Sets the colours and "About us" on the visiting card.</div>
                </div>
                <div class="col-12">
                    <label class="form-label">Photo</label>
                    <div class="d-flex align-items-center gap-3">
                        <img id="photoPreview" src="{{ $user->photo ? asset('storage/'.$user->photo) : '' }}" alt="" style="width:64px;height:64px;border-radius:50%;object-fit:cover;background:#f4f6fa;{{ $user->photo ? '' : 'display:none' }}">
                        <input type="file" name="photo" id="photoInput" class="form-control {{ $err('photo') }}" accept="image/*">
                    </div>
                    @error('photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <div class="form-text">A clear, square face photo looks best on the visiting card.</div>
                </div>
            </div>
        </section>

        {{-- CONTACT --}}
        <section class="kp-panel kp-section" id="sec-contact">
            <div class="kp-panel-h"><h2><i class="bx bx-phone-call"></i>Contact</h2></div>
            <div class="kp-panel-b row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Official mobile</label>
                    <input type="tel" inputmode="tel" name="official_contact" value="{{ $v('official_contact') }}" class="form-control {{ $err('official_contact') }}" placeholder="10-digit number">
                    <div class="form-text">Used for sign-in, the visiting card and KeyLeads.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Personal mobile</label>
                    <input type="tel" inputmode="tel" name="personal_contact" value="{{ $v('personal_contact') }}" class="form-control {{ $err('personal_contact') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Official email</label>
                    <input type="email" inputmode="email" name="official_email" value="{{ $v('official_email') }}" class="form-control {{ $err('official_email') }}">
                    @error('official_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Also the sign-in email.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Personal email</label>
                    <input type="email" inputmode="email" name="personal_email" value="{{ $v('personal_email') }}" class="form-control {{ $err('personal_email') }}">
                </div>
            </div>
        </section>

        {{-- JOB --}}
        <section class="kp-panel kp-section" id="sec-job">
            <div class="kp-panel-h"><h2><i class="bx bx-briefcase"></i>Job</h2></div>
            <div class="kp-panel-b row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Department *</label>
                    <select name="department_id" class="form-select {{ $err('department_id') }}" required>
                        <option value="">Select department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ $v('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Designation *</label>
                    <select name="designation_id" class="form-select {{ $err('designation_id') }}" required>
                        <option value="">Select designation</option>
                        @foreach($designations as $designation)
                            <option value="{{ $designation->id }}" {{ $v('designation_id') == $designation->id ? 'selected' : '' }}>{{ $designation->name }}</option>
                        @endforeach
                    </select>
                    @error('designation_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">System role *</label>
                    <select name="role_id" class="form-select {{ $err('role_id') }}" required>
                        <option value="">Select role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $v('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <div class="form-text">Decides which RealtySuite modules they can open.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Reporting manager</label>
                    <select name="reporting_manager_id" id="reporting_manager_id" class="form-select" data-original="{{ $user->reporting_manager_id }}">
                        <option value="">No manager</option>
                        @foreach($reportingUsers as $mgr)
                            <option value="{{ $mgr->id }}" {{ $v('reporting_manager_id') == $mgr->id ? 'selected' : '' }}>{{ $mgr->full_name ?? $mgr->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 kp-mgr-change" style="{{ old('reporting_manager_id', $user->reporting_manager_id) != $user->reporting_manager_id ? '' : 'display:none' }}">
                    <div class="row g-3 p-2 rounded" style="background:var(--kp-gold-soft, #faf6e3);margin:0">
                        <div class="col-12 col-md-4">
                            <label class="form-label">New manager from</label>
                            <input type="date" name="manager_effective_from" class="form-control" value="{{ old('manager_effective_from', now()->toDateString()) }}">
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="form-label">Reason for change</label>
                            <input type="text" name="manager_change_note" maxlength="190" class="form-control" value="{{ old('manager_change_note') }}" placeholder="e.g. Team restructure">
                        </div>
                        <div class="col-12 form-text mt-1">Saved in the reporting history. Bookings already made keep the old team.</div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Work location *</label>
                    <select name="work_location_id" class="form-select {{ $err('work_location_id') }}" required>
                        <option value="">Select location</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ $v('work_location_id') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                        @endforeach
                    </select>
                    @error('work_location_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Location handled</label>
                    <input type="text" name="location_handled" value="{{ $v('location_handled') }}" class="form-control" placeholder="e.g. Thane West">
                </div>

                <div class="col-12"><hr class="my-1"><div class="fw-bold" style="color:var(--kp-navy)">Dates</div></div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Joining date *</label>
                    <input type="date" name="joining_date" id="joining_date" value="{{ $dateVal('joining_date') }}" class="form-control {{ $err('joining_date') }}" required>
                    @error('joining_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Probation (days)</label>
                    <input type="number" inputmode="numeric" name="probation_period_days" id="probation_period_days" value="{{ $v('probation_period_days') }}" class="form-control">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Confirmation date</label>
                    <input type="date" name="confirm_date" id="confirm_date" value="{{ $dateVal('confirm_date') }}" class="form-control readonly-calc" readonly>
                    <div class="form-text">Joining date + probation.</div>
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Employment status</label>
                    <select name="employment_status" class="form-select raw-select">
                        <option value="probation" {{ strtolower((string) $v('employment_status')) === 'probation' ? 'selected' : '' }}>Probation</option>
                        <option value="confirmed" {{ strtolower((string) $v('employment_status')) === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    </select>
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Notice period (days)</label>
                    <input type="number" inputmode="numeric" name="notice_period_days" value="{{ $v('notice_period_days') }}" class="form-control">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Weekly off</label>
                    <input type="text" name="work_off" value="{{ $v('work_off') }}" class="form-control" list="offList" placeholder="e.g. Tuesday">
                    <datalist id="offList">@foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d)<option value="{{ $d }}">@endforeach</datalist>
                </div>

                <div class="col-12"><hr class="my-1"><div class="fw-bold" style="color:var(--kp-navy)">Exit <span class="text-muted fw-normal small">(only when leaving)</span></div></div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Leaving date</label>
                    <input type="date" name="leaving_date" class="form-control" value="{{ old('leaving_date', $user->leaving_date ? \Carbon\Carbon::parse($user->leaving_date)->format('Y-m-d') : ($user->latestExitHistory?->exit_date ? \Carbon\Carbon::parse($user->latestExitHistory->exit_date)->format('Y-m-d') : '')) }}">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Exit type</label>
                    <select name="exit_status" class="form-select raw-select">
                        <option value="">Not leaving</option>
                        @foreach(['Resigned','Terminated','Absconded','Retired'] as $status)
                            <option value="{{ $status }}" {{ old('exit_status', $user->exit_status ?? $user->latestExitHistory?->exit_type) == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Full &amp; final</label>
                    <select name="fnf_status" class="form-select raw-select">
                        <option value="">Not started</option>
                        @foreach(['Pending', 'In progress', 'Settled'] as $s)
                            <option value="{{ $s }}" {{ $v('fnf_status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                        @if($v('fnf_status') && ! in_array($v('fnf_status'), ['Pending', 'In progress', 'Settled'], true))
                            <option value="{{ $v('fnf_status') }}" selected>{{ $v('fnf_status') }}</option>
                        @endif
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Reason for leaving</label>
                    <input type="text" name="reason_for_leaving" class="form-control" value="{{ old('reason_for_leaving', $user->reason_for_leaving ?? $user->latestExitHistory?->exit_reason) }}">
                </div>
            </div>
        </section>

        {{-- PAY --}}
        <section class="kp-panel kp-section" id="sec-pay">
            <div class="kp-panel-h"><h2><i class="bx bx-wallet"></i>Pay</h2><span class="kp-note">Breakup is worked out from annual CTC</span></div>
            <div class="kp-panel-b row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Annual CTC (₹)</label>
                    <input type="number" step="0.01" inputmode="decimal" name="annual_ctc" id="annual_ctc" value="{{ $v('annual_ctc') }}" class="form-control" data-original="{{ $user->annual_ctc }}">
                    <div class="form-text" id="ctcWords"></div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Monthly CTC (₹)</label>
                    <input type="number" step="0.01" name="current_ctc" id="current_ctc" value="{{ $v('current_ctc') }}" class="form-control readonly-calc" readonly>
                </div>

                <div class="col-12 kp-ctc-change" style="{{ old('annual_ctc', $user->annual_ctc) != $user->annual_ctc ? '' : 'display:none' }}">
                    <div class="row g-3 p-2 rounded" style="background:var(--kp-gold-soft, #faf6e3);margin:0">
                        <div class="col-12 col-md-4">
                            <label class="form-label">New CTC from</label>
                            <input type="date" name="salary_effective_from" class="form-control" value="{{ old('salary_effective_from', now()->startOfMonth()->toDateString()) }}">
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="form-label">Reason</label>
                            <input type="text" name="salary_change_reason" maxlength="190" class="form-control" list="ctcReasons" value="{{ old('salary_change_reason') }}" placeholder="e.g. Annual increment">
                            <datalist id="ctcReasons"><option value="Annual increment"><option value="Promotion"><option value="Correction"><option value="Onboarding"></datalist>
                        </div>
                        <div class="col-12 form-text mt-1">Saved as a salary revision on the profile.</div>
                    </div>
                </div>

                @php
                    $salary_fields = [
                        'monthly_basic' => 'Basic', 'monthly_hra' => 'HRA', 'special_allowance' => 'Special allowance',
                        'conveyance_allowance' => 'Conveyance', 'medical_reimbursement' => 'Medical',
                        'professional_tax' => 'Professional tax', 'pf_employer' => 'PF (employer)', 'pf_employee' => 'PF (employee)',
                        'net_deductions' => 'Total deductions', 'net_salary' => 'Take-home',
                    ];
                @endphp
                @foreach($salary_fields as $field => $label)
                    <div class="col-6 col-md-3">
                        <label class="form-label">{{ $label }}</label>
                        <input type="text" name="{{ $field }}" id="{{ $field }}" value="{{ $v($field) }}" class="form-control readonly-calc {{ $field === 'net_salary' ? 'fw-bold' : '' }}" readonly>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- BANK & IDS --}}
        <section class="kp-panel kp-section" id="sec-bank">
            <div class="kp-panel-h"><h2><i class="bx bx-credit-card"></i>Bank &amp; IDs</h2></div>
            <div class="kp-panel-b row g-3">
                <div class="col-6 col-md-4">
                    <label class="form-label">PF</label>
                    <select name="pf_status" id="pf_status" class="form-select raw-select {{ $err('pf_status') }}">
                        <option value="1" {{ (string) $v('pf_status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (string) $v('pf_status') !== '1' ? 'selected' : '' }}>Not active</option>
                    </select>
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">PF joining date</label>
                    <input type="date" name="pf_joining_date" value="{{ $dateVal('pf_joining_date') }}" class="form-control">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">UAN</label>
                    <input type="text" inputmode="numeric" name="uan_number" value="{{ $v('uan_number') }}" class="form-control {{ $err('uan_number') }}" maxlength="12">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">PAN</label>
                    <input type="text" name="pan_no" value="{{ $v('pan_no') }}" class="form-control text-uppercase" maxlength="10" pattern="[A-Za-z]{5}[0-9]{4}[A-Za-z]" placeholder="ABCDE1234F" autocapitalize="characters">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Aadhaar</label>
                    <input type="text" inputmode="numeric" name="aadhar_no" value="{{ $v('aadhar_no') }}" class="form-control" maxlength="14" placeholder="12 digits">
                </div>

                <div class="col-12"><hr class="my-1"><div class="fw-bold" style="color:var(--kp-navy)">Salary account</div></div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Name as per bank</label>
                    <input type="text" name="bank_account_name" value="{{ $v('bank_account_name') }}" class="form-control {{ $err('bank_account_name') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Account number</label>
                    <input type="text" inputmode="numeric" name="bank_account_number" value="{{ $v('bank_account_number') }}" class="form-control {{ $err('bank_account_number') }}" autocomplete="off">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">IFSC</label>
                    <input type="text" name="ifsc_code" value="{{ $v('ifsc_code') }}" class="form-control text-uppercase {{ $err('ifsc_code') }}" maxlength="11" placeholder="HDFC0001234" autocapitalize="characters">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Account type</label>
                    <select name="bank_account_type" class="form-select raw-select {{ $err('bank_account_type') }}">
                        <option value="">Select</option>
                        @foreach(['salary' => 'Salary', 'savings' => 'Savings', 'current' => 'Current'] as $k => $l)
                            <option value="{{ $k }}" {{ $v('bank_account_type') == $k ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Bank</label>
                    <input type="text" name="bank_name" value="{{ $v('bank_name') }}" class="form-control {{ $err('bank_name') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Branch</label>
                    <input type="text" name="bank_branch_name" value="{{ $v('bank_branch_name') }}" class="form-control {{ $err('bank_branch_name') }}">
                </div>
            </div>
        </section>

        {{-- DOCUMENTS --}}
        <section class="kp-panel kp-section" id="sec-documents">
            <div class="kp-panel-h"><h2><i class="bx bx-file"></i>Documents</h2></div>
            <div class="kp-panel-b row g-3">
                @foreach([
                    ['offer', 'Offer letter'],
                    ['joining', 'Joining letter'],
                ] as [$k, $title])
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded" style="border:1px solid var(--kp-line)">
                            <div class="fw-bold mb-2" style="color:var(--kp-navy)">{{ $title }}</div>
                            <div class="kp-switch">
                                <span>Sent</span>
                                <input type="hidden" name="{{ $k }}_letter_sent" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" name="{{ $k }}_letter_sent" value="1" {{ $v($k.'_letter_sent') ? 'checked' : '' }}>
                            </div>
                            <div class="kp-switch">
                                <span>Signed / acknowledged</span>
                                <input type="hidden" name="{{ $k }}_letter_acknowledged" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" name="{{ $k }}_letter_acknowledged" value="1" {{ $v($k.'_letter_acknowledged') ? 'checked' : '' }}>
                            </div>
                            <input type="file" name="{{ $k }}_letter_file" class="form-control mt-2" accept=".pdf,.doc,.docx,image/*">
                            @if($user->{$k.'_letter_file'})
                                <div class="form-text">Current file: <a href="{{ asset('storage/'.$user->{$k.'_letter_file'}) }}" target="_blank">open</a>. Uploading a new one replaces it.</div>
                            @endif
                        </div>
                    </div>
                @endforeach
                <div class="col-12">
                    <label class="form-label">Previous employment documents</label>
                    <input type="file" name="previous_documents[]" class="form-control" multiple>
                    <div class="form-text">Relieving letter, last payslips and so on. New files are added; nothing is removed.</div>
                    @if($user->previousEmploymentDocuments->count())
                        <ul class="kp-list mt-2">
                            @foreach($user->previousEmploymentDocuments as $doc)
                                <li>
                                    <div class="ic"><i class="bx bx-paperclip"></i></div>
                                    <div class="bd"><div class="t">{{ basename($doc->file_path) }}</div><div class="m">Added {{ F::date($doc->created_at) }}</div></div>
                                    <div class="r"><a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Open</a></div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </section>

        {{-- PERSONAL --}}
        <section class="kp-panel kp-section" id="sec-personal">
            <div class="kp-panel-h"><h2><i class="bx bx-home-heart"></i>Personal</h2></div>
            <div class="kp-panel-b row g-3">
                <div class="col-6 col-md-3">
                    <label class="form-label">Blood group</label>
                    <input type="text" name="blood_group" value="{{ $v('blood_group') }}" class="form-control" list="bloodList" maxlength="5">
                    <datalist id="bloodList">@foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $b)<option value="{{ $b }}">@endforeach</datalist>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label">Marital status</label>
                    <input type="text" name="marital_status" value="{{ $v('marital_status') }}" class="form-control" list="maritalList">
                    <datalist id="maritalList">@foreach(config('constants.MARITAL_STATUS_OPTIONS', []) as $l)<option value="{{ $l }}">@endforeach</datalist>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label">Marriage date</label>
                    <input type="date" name="marriage_date" value="{{ $dateVal('marriage_date') }}" class="form-control">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label">Spouse name</label>
                    <input type="text" name="spouse_name" value="{{ $v('spouse_name') }}" class="form-control">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Education</label>
                    <input type="text" name="education_qualification" value="{{ $v('education_qualification') }}" class="form-control" placeholder="e.g. B.Com">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Languages known</label>
                    <input type="text" name="languages_known" value="{{ $v('languages_known') }}" class="form-control" placeholder="e.g. English, Hindi, Marathi">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Current address</label>
                    <textarea name="communication_address" id="communication_address" class="form-control" rows="2">{{ $v('communication_address') }}</textarea>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label d-flex justify-content-between">
                        <span>Permanent address</span>
                        <a href="#" class="small" onclick="document.getElementById('permanent_address').value = document.getElementById('communication_address').value; return false;">Same as current</a>
                    </label>
                    <textarea name="permanent_address" id="permanent_address" class="form-control" rows="2">{{ $v('permanent_address') }}</textarea>
                </div>

                <div class="col-12"><hr class="my-1"><div class="fw-bold" style="color:var(--kp-navy)">Emergency contact</div></div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Name</label>
                    <input type="text" name="emergency_contact_name" value="{{ $v('emergency_contact_name') }}" class="form-control">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Relationship</label>
                    <input type="text" name="emergency_contact_relationship" value="{{ $v('emergency_contact_relationship') }}" class="form-control" list="relList">
                    <datalist id="relList">@foreach(['Father','Mother','Spouse','Brother','Sister','Friend'] as $r)<option value="{{ $r }}">@endforeach</datalist>
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Number</label>
                    <input type="tel" inputmode="tel" name="emergency_contact_number" value="{{ $v('emergency_contact_number') }}" class="form-control">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Parents' contact</label>
                    <input type="tel" inputmode="tel" name="parents_contact" value="{{ $v('parents_contact') }}" class="form-control">
                </div>
            </div>
        </section>

        {{-- ASSETS --}}
        <section class="kp-panel kp-section" id="sec-assets">
            <div class="kp-panel-h"><h2><i class="bx bx-laptop"></i>Company assets &amp; notes</h2></div>
            <div class="kp-panel-b row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label">Laptop / desktop</label>
                    <input type="text" name="laptop_desktop" value="{{ $v('laptop_desktop') }}" class="form-control" placeholder="e.g. Dell Latitude, tag KS-014">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Company phone</label>
                    <input type="text" name="company_phone" value="{{ $v('company_phone') }}" class="form-control" placeholder="Model / IMEI">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Company SIM</label>
                    <input type="text" name="company_sim" value="{{ $v('company_sim') }}" class="form-control" placeholder="Number">
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="additional_comments" class="form-control" rows="2">{{ $v('additional_comments') }}</textarea>
                </div>
            </div>
        </section>

        <div class="kp-savebar">
            <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Save changes</button>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var $id = function (id) { return document.getElementById(id); };

    // Confirmation date = joining date + probation days
    var joiningDate = $id('joining_date'), probationDays = $id('probation_period_days'), confirmDate = $id('confirm_date');
    function calculateConfirmDate() {
        if (!joiningDate.value || !probationDays.value) return;
        var d = new Date(joiningDate.value);
        d.setDate(d.getDate() + parseInt(probationDays.value, 10));
        confirmDate.value = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }
    probationDays.addEventListener('input', calculateConfirmDate);
    joiningDate.addEventListener('change', calculateConfirmDate);
    calculateConfirmDate();

    // Annual CTC -> monthly breakup (same rules as before; PF only when PF is active)
    var annualEl = $id('annual_ctc');
    function inWords(n) {
        if (!n) return '';
        if (n >= 10000000) return '₹ ' + (n / 10000000).toFixed(2).replace(/\.?0+$/, '') + ' crore a year';
        if (n >= 100000) return '₹ ' + (n / 100000).toFixed(2).replace(/\.?0+$/, '') + ' lakh a year';
        return '₹ ' + Math.round(n).toLocaleString('en-IN') + ' a year';
    }
    function calcCtc(fromUser) {
        var annual = parseFloat(annualEl.value || 0);
        $id('ctcWords').textContent = inWords(annual);
        document.querySelector('.kp-ctc-change').style.display =
            String(parseFloat(annualEl.value || 0)) !== String(parseFloat(annualEl.dataset.original || 0)) ? '' : 'none';
        if (!fromUser || annual <= 0) return;

        var monthly = annual / 12;
        var basic = monthly * 0.50, hra = basic * 0.50, special = monthly * 0.10, convey = monthly * 0.10, medical = monthly * 0.05;
        var pfOn = $id('pf_status').value === '1';
        var pfEmployer = pfOn ? 1800 : 0, pfEmployee = pfOn ? 1800 : 0;
        var gender = ($id('gender').value || '').toLowerCase();
        var profTax = (monthly < 25000 && gender === 'female') ? 0 : 200;
        var deductions = pfEmployee + pfEmployer + profTax;
        var net = monthly - deductions;

        $id('current_ctc').value = monthly.toFixed(2);
        $id('monthly_basic').value = basic.toFixed(2);
        $id('monthly_hra').value = hra.toFixed(2);
        $id('special_allowance').value = special.toFixed(2);
        $id('conveyance_allowance').value = convey.toFixed(2);
        $id('medical_reimbursement').value = medical.toFixed(2);
        $id('professional_tax').value = profTax.toFixed(2);
        $id('pf_employer').value = pfEmployer.toFixed(2);
        $id('pf_employee').value = pfEmployee.toFixed(2);
        $id('net_deductions').value = deductions.toFixed(2);
        $id('net_salary').value = net.toFixed(2);
    }
    annualEl.addEventListener('input', function () { calcCtc(true); });
    $id('pf_status').addEventListener('change', function () { calcCtc(true); });
    $id('gender').addEventListener('change', function () { calcCtc(true); });
    calcCtc(false);

    // Show "new manager from / reason" only when the manager changes
    var mgr = $id('reporting_manager_id');
    function mgrChanged() {
        document.querySelector('.kp-mgr-change').style.display = String(mgr.value) !== String(mgr.dataset.original || '') ? '' : 'none';
    }
    mgr.addEventListener('change', mgrChanged);
    if (window.jQuery) jQuery(mgr).on('changed.bs.select', mgrChanged);

    // Photo preview
    $id('photoInput').addEventListener('change', function () {
        var f = this.files && this.files[0];
        if (!f) return;
        var img = $id('photoPreview');
        img.src = URL.createObjectURL(f);
        img.style.display = '';
    });

    // Section jump bar: highlight where you are
    var links = document.querySelectorAll('#kpJump a');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (!e.isIntersecting) return;
                links.forEach(function (a) {
                    var on = a.dataset.sec === e.target.id;
                    a.classList.toggle('on', on);
                    if (on) a.scrollIntoView({ block: 'nearest', inline: 'center' });
                });
            });
        }, { rootMargin: '-30% 0px -60% 0px' });
        document.querySelectorAll('.kp-section').forEach(function (s) { io.observe(s); });
    }

    // After a failed save, jump to the first field with a problem
    var bad = document.querySelector('.is-invalid');
    if (bad) setTimeout(function () { bad.scrollIntoView({ block: 'center' }); }, 200);

    // Warn before leaving with unsaved changes
    var form = $id('userForm'), dirty = false;
    form.addEventListener('input', function () { dirty = true; });
    form.addEventListener('change', function () { dirty = true; });
    form.addEventListener('submit', function () { dirty = false; });
    window.addEventListener('beforeunload', function (e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
});
</script>
@endsection

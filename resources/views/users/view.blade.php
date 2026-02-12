@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">
            <a href="{{ route('users.index') }}" class="text-muted fw-light">Users</a> / View User
        </h4>
        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">
            ✏️ Edit User
        </a>
    </div>

    <!-- PROFILE CARD -->
    <div class="card mb-4">
        <div class="card-body text-center">
            <img
                src="{{ $user->photo ? asset('storage/'.$user->photo) : asset('assets/img/avatars/profile.png') }}"
                class="rounded-circle mb-2"
                width="110"
                height="110"
            >
            <h5 class="mb-0">{{ $user->full_name ?? '-' }}</h5>
            <small class="text-muted">{{ $user->employee_code ?? '-' }}</small>

            <div class="mt-2">
                <span class="badge bg-primary">{{ $user->entity ?? '-' }}</span>
                <span class="badge bg-info">{{ $user->department?->name ?? 'No Dept' }}</span>
                <span class="badge {{ $user->status === 'Exited' ? 'bg-danger' : 'bg-success' }}">
                    {{ $user->status }}
                </span>
            </div>
        </div>
    </div>

    <!-- BASIC INFORMATION -->
    <div class="card mb-4">
        <div class="card-header fw-bold">🧩 Basic Information</div>
        <div class="card-body row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>Title</th><td>{{ $user->title ?? '-' }}</td></tr>
                    <tr><th>First Name</th><td>{{ $user->first_name ?? '-' }}</td></tr>
                    <tr><th>Middle Name</th><td>{{ $user->middle_name ?? '-' }}</td></tr>
                    <tr><th>Last Name</th><td>{{ $user->last_name ?? '-' }}</td></tr>
                    <tr><th>Gender</th><td>{{ $user->gender ?? '-' }}</td></tr>
                    <tr><th>DOB</th><td>{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('d-m-Y') : '-' }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>Employee Code</th><td>{{ $user->employee_code ?? '-' }}</td></tr>
                    <tr><th>Company</th><td>{{ $user->entity ?? '-' }}</td></tr>
                    <tr><th>Business Unit</th><td>{{ $user->businessUnit?->name ?? '-' }}</td></tr>
                    <tr><th>Status</th><td>{{ $user->status ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <!-- CONTACT INFORMATION -->
    <div class="card mb-4">
        <div class="card-header fw-bold">☎️ Contact Information</div>
        <div class="card-body row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>Official Contact</th><td>{{ $user->official_contact ?? '-' }}</td></tr>
                    <tr><th>Personal Contact</th><td>{{ $user->personal_contact ?? '-' }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>Official Email</th><td>{{ $user->official_email ?? '-' }}</td></tr>
                    <tr><th>Personal Email</th><td>{{ $user->personal_email ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <!-- EMPLOYMENT DETAILS -->
    <div class="card mb-4">
        <div class="card-header fw-bold">🏢 Employment Details</div>
        <div class="card-body row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>Department</th><td>{{ $user->department?->name ?? '-' }}</td></tr>
                    <tr><th>Designation</th><td>{{ $user->designation?->name ?? '-' }}</td></tr>
                    <tr><th>Role</th><td>{{ $user->getRoleNames()->first() ?? '-' }}</td></tr>
                    <tr><th>Reporting Manager</th>  <td>{{ $user->currentReportingManagerHistory?->manager?->name ?? 'Not Assigned' }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>Joining Date</th><td>{{ $user->joining_date ?? '-' }}</td></tr>
                    <tr><th>Probation Days</th><td>{{ $user->probation_period_days ?? '-' }}</td></tr>
                    <tr><th>Confirmation Date</th><td>{{ $user->confirm_date ?? '-' }}</td></tr>
                    <tr><th>Employment Status</th><td>{{ $user->employment_status ?? '-' }}</td></tr>
                    <tr><th>Leaving Date</th><td>{{ $user->leaving_date ?? '-' }}</td></tr>
                    <tr><th>Notice Period</th><td>{{ $user->notice_period_days ?? '-' }}</td></tr>
                    <tr><th>Work Location</th><td>{{ $user->location?->name ?? '-' }}</td></tr>
                    <tr><th>Location Handled</th><td>{{ $user->location_handled ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <!-- SALARY & COMPENSATION -->
    <div class="card mb-4">
        <div class="card-header fw-bold">💰 Salary & Compensation</div>
        <div class="card-body row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>Annual CTC</th><td>{{ number_format($user->annual_ctc,2) }}</td></tr>
                    <tr><th>Monthly CTC</th><td>{{ number_format($user->current_ctc,2) }}</td></tr>
                    <tr><th>Net Salary</th><td>{{ number_format($user->net_salary,2) }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>Basic</th><td>{{ $user->monthly_basic }}</td></tr>
                    <tr><th>HRA</th><td>{{ $user->monthly_hra }}</td></tr>
                    <tr><th>Special Allowance</th><td>{{ $user->special_allowance }}</td></tr>
                    <tr><th>Conveyance</th><td>{{ $user->conveyance_allowance }}</td></tr>
                    <tr><th>Medical</th><td>{{ $user->medical_reimbursement }}</td></tr>
                    <tr><th>Professional Tax</th><td>{{ $user->professional_tax }}</td></tr>
                    <tr><th>PF Employer</th><td>{{ $user->pf_employer }}</td></tr>
                    <tr><th>PF Employee</th><td>{{ $user->pf_employee }}</td></tr>
                    <tr><th>Net Deductions</th><td>{{ $user->net_deductions }}</td></tr>
                </table>
            </div>
        </div>

        @if($user->salaryHistories->count())
        <div class="card-body">
            <h6 class="fw-bold">Salary History</h6>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Effective From</th>
                        <th>Annual CTC</th>
                        <th>Monthly CTC</th>
                        <th>Net Salary</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->salaryHistories as $s)
                        <tr>
                            <td>{{ $s->effective_from }}</td>
                            <td>{{ number_format($s->annual_ctc,2) }}</td>
                            <td>{{ number_format($s->annual_ctc / 12, 2) }}</td>
                            <td>{{ number_format($s->net_salary,2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- REPORTING MANAGER HISTORY -->
    @if($user->reportingManagerHistories->count())
    <div class="card mb-4">
        <div class="card-header fw-bold">👔 Reporting Manager History</div>
        <div class="card-body">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Manager</th>
                        <th>From</th>
                        <th>To</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach($user->reportingManagerHistories as $history)
                        <tr>
                            <td>
                                {{ $history->manager?->name ?? 'N/A' }}
                            </td>
                            <td>{{ $history->effective_from }}</td>
                            <td>{{ $history->effective_to ?? 'Present' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- EXIT DETAILS -->
    @if($user->latestExitHistory)
    <div class="card mb-4 border-danger">
        <div class="card-header fw-bold text-danger">🚪 Exit Details</div>
        <div class="card-body">
            <table class="table table-borderless">
                <tr><th>Exit Date</th><td>{{ $user->latestExitHistory->exit_date }}</td></tr>
                <tr><th>Exit Type</th><td>{{ $user->latestExitHistory->exit_type }}</td></tr>
                <tr><th>Reason</th><td>{{ $user->latestExitHistory->exit_reason }}</td></tr>
            </table>
        </div>
    </div>
    @endif

    <!-- BANKING -->
    <div class="card mb-4">
        <div class="card-header fw-bold">🏦 Statutory & Banking</div>
        <div class="card-body row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>PF Status</th><td>{{ $user->pf_status ?? '-' }}</td></tr>
                    <tr><th>UAN</th><td>{{ $user->uan_number ?? '-' }}</td></tr>
                    <tr><th>Name as per Bank</th><td>{{ $user->bank_account_name ?? '-' }}</td></tr>
                    <tr><th>Bank Branch</th><td>{{ $user->bank_branch_name ?? '-' }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>Account Type</th><td>{{ $user->bank_account_type ?? '-' }}</td></tr>
                    <tr><th>Bank Name</th><td>{{ $user->bank_name ?? '-' }}</td></tr>
                    <tr><th>IFSC</th><td>{{ $user->ifsc_code ?? '-' }}</td></tr>
                    <tr><th>Account Number</th><td>{{ $user->bank_account_number ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <!-- PERSONAL & EMERGENCY -->
    <div class="card mb-4">
        <div class="card-header fw-bold">🏠 Personal & Emergency Info</div>
        <div class="card-body row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>Blood Group</th><td>{{ $user->blood_group ?? '-' }}</td></tr>
                    <tr><th>Languages Known</th><td>{{ $user->languages_known ?? '-' }}</td></tr>
                    <tr><th>Education</th><td>{{ $user->education_qualification ?? '-' }}</td></tr>
                    <tr><th>Marital Status</th><td>{{ $user->marital_status ?? '-' }}</td></tr>
                    <tr><th>Marriage Date</th><td>{{ $user->marriage_date ?? '-' }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>Spouse Name</th><td>{{ $user->spouse_name ?? '-' }}</td></tr>
                    <tr><th>Parents Contact</th><td>{{ $user->parents_contact ?? '-' }}</td></tr>
                    <tr><th>Emergency Contact</th><td>{{ $user->emergency_contact_name ?? '-' }}</td></tr>
                    <tr><th>Emergency Relationship</th><td>{{ $user->emergency_contact_relationship ?? '-' }}</td></tr>
                    <tr><th>Emergency Number</th><td>{{ $user->emergency_contact_number ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <!-- ASSETS & MISC -->
    <div class="card mb-4">
        <div class="card-header fw-bold">💻 Company Assets & Miscellaneous</div>
        <div class="card-body">
            <table class="table table-borderless">
                <tr><th>Work Off</th><td>{{ $user->work_off ?? '-' }}</td></tr>
                <tr><th>Additional Comments</th><td>{{ $user->additional_comments ?? '-' }}</td></tr>
            </table>
        </div>
    </div>

</div>
@endsection

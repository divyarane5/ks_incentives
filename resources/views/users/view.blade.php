    @extends('layouts.app')

    @section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">
                <a href="{{ route('users.index') }}" class="text-muted fw-light">Users</a> /
            </span> View
        </h4>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header text-center">User Details - {{ $user->employee_code }}</h5>
                    <div class="card-body text-center">
                        <!-- Centered Profile Image -->
                        <img
                            src="{{ ($user->photo != '') ? url('storage/app/'.$user->photo) : asset('assets/img/avatars/profile.png') }}"
                            alt="user-avatar"
                            class="rounded"
                            height="100"
                            width="100"
                        />
                    </div>

                    <hr />

                    <!-- Basic Information -->
                    <div class="mb-4">
                        <h5 class="card-header">Basic Information</h5>
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <table class="table table-borderless">
                                    <tr><th width="40%">Name:</th><td>{{ $user->name ?? '-' }}</td></tr>
                                    <tr><th>E-mail:</th><td>{{ $user->email ?? '-' }}</td></tr>
                                    <tr><th>Gender:</th><td>{{ $user->gender ?? '-' }}</td></tr>
                                    <tr><th>Date of Birth:</th><td>{{ $user->dob ?? '-' }}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr><th>Joining Date:</th><td>{{ $user->joining_date ?? '-' }}</td></tr>
                                    <tr><th>Confirmation Date:</th><td>{{ $user->confirm_date ?? '-' }}</td></tr>
                                    <tr><th>Leaving Date:</th><td>{{ $user->leaving_date ?? '-' }}</td></tr>
                                    <tr><th>Exit Status:</th><td>{{ $user->exit_status ?? '-' }}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <!-- Work Information -->
                    <div class="mb-4">
                        <h5 class="card-header">Work Information</h5>
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <table class="table table-borderless">
                                    <tr><th>Company:</th><td>{{ $user->entity ?? '-' }}</td></tr>
                                    <tr><th>Role:</th><td>{{ $user->getRoleNames()[0] ?? '-' }}</td></tr>
                                    <tr><th>Reporting To:</th><td>{{ $user->reportingTo->name ?? '-' }}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr><th>Location:</th><td>{{ $user->location->name ?? '-' }}</td></tr>
                                    <tr><th>Department:</th><td>{{ $user->department->name ?? '-' }}</td></tr>
                                    <tr><th>Designation:</th><td>{{ $user->designation->name ?? '-' }}</td></tr>
                                    <tr><th>Work Location Handled:</th><td>{{ $user->location_handled ?? '-' }}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <!-- Salary & Compensation -->
                    <div class="mb-4">
                        <h5 class="card-header">Salary & Compensation</h5>
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <table class="table table-borderless">
                                    <tr><th>Current CTC (Annual):</th><td>{{ $user->current_ctc ?? '-' }}</td></tr>
                                    <tr><th>Monthly Basic:</th><td>{{ $user->monthly_basic ?? '-' }}</td></tr>
                                    <tr><th>Monthly HRA:</th><td>{{ $user->monthly_hra ?? '-' }}</td></tr>
                                    <tr><th>Special Allowance:</th><td>{{ $user->special_allowance ?? '-' }}</td></tr>
                                    <tr><th>Conveyance Allowance:</th><td>{{ $user->conveyance_allowance ?? '-' }}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr><th>Medical Reimbursement:</th><td>{{ $user->medical_reimbursement ?? '-' }}</td></tr>
                                    <tr><th>Professional Tax:</th><td>{{ $user->professional_tax ?? '-' }}</td></tr>
                                    <tr><th>PF Employer:</th><td>{{ $user->pf_employer ?? '-' }}</td></tr>
                                    <tr><th>PF Employee:</th><td>{{ $user->pf_employee ?? '-' }}</td></tr>
                                    <tr><th>Net Salary:</th><td>{{ $user->net_salary ?? '-' }}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <!-- Statutory & Banking Details -->
                    <div class="mb-4">
                        <h5 class="card-header">Statutory & Banking Details</h5>
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <table class="table table-borderless">
                                    <tr><th>PF Status:</th><td>{{ $user->pf_status ?? '-' }}</td></tr>
                                    <tr><th>UAN Number:</th><td>{{ $user->uan_number ?? '-' }}</td></tr>
                                    <tr><th>Bank Name:</th><td>{{ $user->bank_name ?? '-' }}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr><th>IFSC Code:</th><td>{{ $user->ifsc_code ?? '-' }}</td></tr>
                                    <tr><th>Bank Account Number:</th><td>{{ $user->bank_account_number ?? '-' }}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <!-- Personal & Emergency Info -->
                    <div class="mb-4">
                        <h5 class="card-header">Personal & Emergency Info</h5>
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <table class="table table-borderless">
                                    <tr><th>Marital Status:</th><td>{{ $user->marital_status ?? '-' }}</td></tr>
                                    <tr><th>Marriage Date:</th><td>{{ $user->marriage_date ?? '-' }}</td></tr>
                                    <tr><th>Spouse Name:</th><td>{{ $user->spouse_name ?? '-' }}</td></tr>
                                    <tr><th>Parents Contact:</th><td>{{ $user->parents_contact ?? '-' }}</td></tr>
                                    <tr><th>Emergency Contact Name:</th><td>{{ $user->emergency_contact_name ?? '-' }}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr><th>Emergency Contact Relationship:</th><td>{{ $user->emergency_contact_relationship ?? '-' }}</td></tr>
                                    <tr><th>Emergency Contact Number:</th><td>{{ $user->emergency_contact_number ?? '-' }}</td></tr>
                                    <tr><th>PAN No:</th><td>{{ $user->pan_no ?? '-' }}</td></tr>
                                    <tr><th>Aadhar No:</th><td>{{ $user->aadhar_no ?? '-' }}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <!-- Company Assets & Miscellaneous -->
                    <div class="mb-4">
                        <h5 class="card-header">Company Assets & Miscellaneous</h5>
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <table class="table table-borderless">
                                    <tr><th>Work Off:</th><td>{{ $user->work_off ?? '-' }}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr><th>Additional Comments:</th><td>{{ $user->additional_comments ?? '-' }}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
    @endsection

@extends('layouts.app')

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><a href="{{ route('users.index') }}" class="text-muted fw-light">Users/</a> Edit User</h4>
    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add User</h5>
            <small class="text-muted float-end"><a class="btn-sm btn-primary" href="{{ route('users.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @form_hidden('id', $user->id)
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">Name<span class="start-mark">*</span></label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ (old('name') != "") ? old('name') : $user->name }}" placeholder="Name" @error('name') autofocus @enderror required />
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="employee_code">Employee Code<span class="start-mark">*</span></label>
                        <input type="text" name="employee_code" class="form-control" id="employee_code" value="{{ (old('employee_code') != "") ? old('employee_code') : $user->employee_code }}" placeholder="Employee Code" @error('employee_code') autofocus @enderror required />
                        @error('employee_code')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="email">Email<span class="start-mark">*</span></label>
                        <input type="text" name="email" class="form-control" id="email" value="{{ (old('email') != "") ? old('email') : $user->email }}" autocomplete="new-email" placeholder="Email" @error('email') autofocus @enderror required/>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" name="password" class="form-control" id="password" value="{{ old('password') }}" autocomplete="new-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" @error('password') autofocus @enderror />
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="entity">Company<span class="start-mark">*</span></label>
                        <select id="entity" name="entity" class="form-select" @error('entity') autofocus @enderror required>
                            <option>Select Company</option>
                            @php
                                $companies = config('constants.COMPANY_OPTIONS');
                            @endphp
                            @foreach ($companies as $key => $company)
                                <option value="{{ $company }}" {{ ($key == ((old('entity') != "") ? old('entity') : $user->entity) ? 'selected' : '') }}>{{ $company }}</option>
                            @endforeach
                        </select>
                        @error('entity')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="location_id">Location<span class="start-mark">*</span></label>
                        <select id="location_id" name="location_id" class="form-select" @error('location_id') autofocus @enderror required>
                            <option>Select Location</option>
                            @if (!empty($locations))
                                @foreach ($locations as $key => $location)
                                    <option value="{{ $location->id }}" {{ ($location->id == ((old('location_id') != "") ? old('location_id') : $user->location_id) ? 'selected' : '') }}>{{ $location->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('location_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="department_id">Department<span class="start-mark">*</span></label>
                        <select id="department_id" name="department_id" class="form-select" @error('department_id') autofocus @enderror required>
                            <option>Select Department</option>
                            @if (!empty($departments))
                                @foreach ($departments as $key => $department)
                                    <option value="{{ $department->id }}" {{ ($department->id == ((old('department_id') != "") ? old('department_id') : $user->department_id) ? 'selected' : '') }}>{{ $department->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('department_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="designation_id">Designation<span class="start-mark">*</span></label>
                        <select id="designation_id" name="designation_id" class="form-select" @error('designation_id') autofocus @enderror required>
                            <option>Select Designation</option>
                            @if (!empty($designations))
                                @foreach ($designations as $key => $designation)
                                    <option value="{{ $designation->id }}" {{ ($designation->id == ((old('designation_id') != "") ? old('designation_id') : $user->designation_id) ? 'selected' : '') }}>{{ $designation->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('designation_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="gender">Gender</label>
                        <select id="gender" name="gender" class="form-select"  @error('gender') autofocus @enderror>
                            <option>Select Gender</option>
                            @php
                                $genders = config('constants.GENDER_OPTIONS');
                            @endphp
                            @foreach ($genders as $key => $gender)
                                <option value="{{ $key }}" {{ ($key == ((old('gender') != "") ? old('gender') : $user->gender) ? 'selected' : '') }}>{{ $gender }}</option>
                            @endforeach
                        </select>
                        @error('gender')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="reporting_user_id">Reporting To</label>
                        <select id="reporting_user_id" name="reporting_user_id" class="form-select" @error('reporting_user_id') autofocus @enderror >
                            <option>Select Reporting</option>
                            @if (!empty($reportingUsers))
                                @foreach ($reportingUsers as $key => $reportingUser)
                                    <option value="{{ $reportingUser->id }}" {{ ($reportingUser->id == ((old('reporting_user_id') != "") ? old('reporting_user_id') : $user->reporting_user_id) ? 'selected' : '') }}>{{ $reportingUser->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('reporting_user_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="dob" class="form-label">Date Of Birth<span class="start-mark">*</span></label>
                        <input class="form-control" name="dob" type="date" value="{{ (old('dob') != "") ? old('dob') : $user->dob }}" id="dob" name="dob" @error('dob') autofocus @enderror required />
                        @error('dob')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="joining_date" class="form-label">Date Of Joining<span class="start-mark">*</span></label>
                        <input class="form-control" type="date" value="{{ (old('joining_date') != "") ? old('joining_date') : $user->joining_date }}" id="joining_date" name="joining_date" @error('joining_date') autofocus @enderror required />
                        @error('joining_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="role_id">Role<span class="start-mark">*</span></label>
                        <select id="role_id" name="role_id" class="form-select" required>
                            <option>Select Role</option>
                            @if (!empty($roles))
                                @foreach ($roles as $key => $role)
                                    <option value="{{ $role->id }}" {{ (old('role_id') != "") ? ((old('role_id') == $role->id) ? 'selected' : '') : (($user->getRoleNames()[0] == $role->name) ? 'selected' : '') }}>{{ $role->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('role_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img
                                src="{{ ($user->photo != "") ? url('storage/app/'.$user->photo) : asset("assets/img/avatars/profile.png") }}"
                                alt="user-avatar"
                                class="d-block rounded"
                                height="100"
                                width="100"
                                id="uploadedAvatar"
                            />
                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Upload new photo</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" name="photo" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg, image/jpg" />
                                </label>
                                <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Reset</span>
                                </button>

                                <p class="text-muted mb-0">Allowed JPG, JPEG or PNG. Max size of 800K</p>
                            </div>
                        </div>
                        {{-- <label for="photo" class="form-label">Photo</label>
                        <input class="form-control" type="file" id="photo" name="photo" accept="image/png, image/jpeg, image/jpg" /> --}}
                        @error('photo')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="mt-2">
                    <button type="submit" class="btn btn-primary me-2">Update changes</button>
                    <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- / Content -->
@endsection

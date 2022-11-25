@extends('layouts.external_layout')

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header justify-content-between align-items-center">
            <h5 class="mb-0">Add Joining Details</h5>
        </div>

        <form action="{{ route('joining_form.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="candidate_id" value="{{ $id }}">
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="joining_date">Date of Joining<span class="start-mark">*</span></label>
                        <input type="date" name="joining_date" class="form-control" id="joining_date"  value="{{ old('joining_date') }}" required/>
                        @error('joining_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="designation">Designation<span class="start-mark">*</span></label>
                        <input type="text" name="designation" class="form-control" id="designation"  value="{{ old('designation') }}" required/>
                        @error('designation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <hr class="my-4">
                    <div class="mb-3 col-md-12 my-4">
                        <h5 class="mb-0">Personal Details</h5>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label" for="first_name">First Name<span class="start-mark">*</span></label>
                        <input type="text" name="first_name" class="form-control" id="first_name"  value="{{ old('first_name') }}" required/>
                        @error('first_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label" for="last_name">Last Name<span class="start-mark">*</span></label>
                        <input type="text" name="last_name" class="form-control" id="last_name"  value="{{ old('last_name') }}" required/>
                        @error('last_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="middle_name">Father/Husband Name<span class="start-mark">*</span></label>
                        <input type="text" name="middle_name" class="form-control" id="middle_name"  value="{{ old('middle_name') }}" required/>
                        @error('middle_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="present_address">Present Address<span class="start-mark">*</span></label>
                        <textarea name="present_address" class="form-control" id="present_address"  required>{{ old('present_address') }}</textarea>
                        @error('present_address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="permanent_address">Permanent Address<span class="start-mark">*</span></label>
                        <textarea  name="permanent_address" class="form-control" id="permanent_address"  required>{{ old('permanent_address') }}</textarea>
                        @error('permanent_address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label" for="mobile">Mobile<span class="start-mark">*</span></label>
                        <input type="text" name="mobile" class="form-control" id="mobile"  value="{{ old('mobile') }}" required/>
                        @error('mobile')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label" for="email">Personal Email ID<span class="start-mark">*</span></label>
                        <input type="text" name="email" class="form-control" id="email"  value="{{ old('email') }}" required/>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label" for="dob">Date of Birth<span class="start-mark">*</span></label>
                        <input type="date" name="dob" class="form-control" id="dob"  value="{{ old('dob') }}" required/>
                        @error('dob')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label" for="marital_status">Marital Status<span class="start-mark">*</span></label>
                        <select id="marital_status" name="marital_status" class="form-select" @error('marital_status') autofocus @enderror>
                            <option value="">Select Marital Status</option>
                            @php
                                $marital_statuses = config('constants.MARITAL_STATUS_OPTIONS');
                            @endphp
                            @foreach ($marital_statuses as $key => $marital_status)
                                <option value="{{ $key }}" {{ ($key == old('marital_status') ? 'selected' : '') }}>{{ $marital_status }}</option>
                            @endforeach
                        </select>
                        @error('marital_status')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="pan_number">PAN No<span class="start-mark">*</span></label>
                                <input type="text" name="pan_number" class="form-control" id="pan_number"  value="{{ old('pan_number') }}" required/>
                                @error('pan_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="blood_group">Blood Group</label>
                                <input type="text" name="blood_group" class="form-control" id="blood_group"  value="{{ old('blood_group') }}" />
                                @error('blood_group')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="aadhar_number">Adhaar No<span class="start-mark">*</span></label>
                                <input type="text" name="aadhar_number" class="form-control" id="aadhar_number"  value="{{ old('aadhar_number') }}" required/>
                                @error('aadhar_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="gender">Gender<span class="start-mark">*</span></label>
                                <select id="gender" name="gender" class="form-select" @error('gender') autofocus @enderror required>
                                    <option value="">Select Gender</option>
                                    @php
                                        $genders = config('constants.GENDER_OPTIONS');
                                    @endphp
                                    @foreach ($genders as $key => $gender)
                                        <option value="{{ $key }}" {{ ($key == old('gender') ? 'selected' : '') }}>{{ $gender }}</option>
                                    @endforeach
                                </select>
                                @error('gender')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 col-md-6">
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img
                                src="{{ asset("assets/img/avatars/profile.png") }}"
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
                        @error('photo')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <hr class="my-4">
                    <div class="mb-3 col-md-12 my-4">
                        <h5 class="mb-0">Emergency Contact Details</h5>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label" for="emergency_contact_name">Name<span class="start-mark">*</span></label>
                        <input type="text" name="emergency_contact_name" class="form-control" id="emergency_contact_name"  value="{{ old('emergency_contact_name') }}" required/>
                        @error('emergency_contact_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label" for="emergency_contact_relation">Relation<span class="start-mark">*</span></label>
                        <input type="text" name="emergency_contact_relation" class="form-control" id="emergency_contact_relation"  value="{{ old('emergency_contact_relation') }}" required/>
                        @error('emergency_contact_relation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label" for="emergency_contact_number">Contact No<span class="start-mark">*</span></label>
                        <input type="text" name="emergency_contact_number" class="form-control" id="emergency_contact_number"  value="{{ old('emergency_contact_number') }}" required/>
                        @error('emergency_contact_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <hr class="my-4">
                    <div class="mb-3 col-md-12 my-4">
                        <h5 class="mb-0">Educational Details</h5>
                    </div>
                    <div class="mb-3 col-md-12" style="overflow: auto">
                        <table class="table table-striped" id="indent_item_table">
                            <thead>
                                <tr style="border-top: 1px solid #DADEE3;">
                                    <th>Degree</th>
                                    <th>University/ Institute</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Percentage/ Grade</th>
                                    <th>Specialization</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @php
                                    $currentYear = date('Y');
                                    $years = range($currentYear, $currentYear-50);
                                @endphp
                                <tr>
                                    <td>
                                        Master’s Degree
                                        <input type="hidden" name="educational_details[0][degree]" value="Master’s Degree" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[0][university]" value="{{ old("educational_details[0][university]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[0][from]" value="{{ old("educational_details[0][from]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[0][to]" value="{{ old("educational_details[0][to]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[0][percentage]" value="{{ old("educational_details[0][percentage]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[0][specialization]" value="{{ old("educational_details[0][specialization]") }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Bachelor’s Degree
                                        <input type="hidden" name="educational_details[1][degree]" value="Bachelor’s Degree" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[1][university]" value="{{ old("educational_details[1][university]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[1][from]" value="{{ old("educational_details[1][from]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[1][to]" value="{{ old("educational_details[1][to]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[1][percentage]" value="{{ old("educational_details[1][percentage]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[1][specialization]" value="{{ old("educational_details[1][specialization]") }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        HSC / 12th
                                        <input type="hidden" name="educational_details[2][degree]" value="HSC / 12th" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[2][university]" value="{{ old("educational_details[2][university]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[2][from]" value="{{ old("educational_details[2][from]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[2][to]" value="{{ old("educational_details[2][to]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[2][percentage]" value="{{ old("educational_details[2][percentage]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[2][specialization]" value="{{ old("educational_details[2][specialization]") }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        SSC / 10th
                                        <input type="hidden" name="educational_details[3][degree]" value="SSC / 10th" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[3][university]" value="{{ old("educational_details[3][university]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[3][from]" value="{{ old("educational_details[3][from]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[3][to]" value="{{ old("educational_details[3][to]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[3][percentage]" value="{{ old("educational_details[3][percentage]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[3][specialization]" value="{{ old("educational_details[3][specialization]") }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Any Other
                                        <input type="hidden" name="educational_details[4][degree]" value="Any Other" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[4][university]" value="{{ old("educational_details[4][university]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[4][from]" value="{{ old("educational_details[4][from]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[4][to]" value="{{ old("educational_details[4][to]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[4][percentage]" value="{{ old("educational_details[4][percentage]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="educational_details[4][specialization]" value="{{ old("educational_details[4][specialization]") }}">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr class="my-4">
                    <div class="mb-3 col-md-12 my-4">
                        <h5 class="mb-0">Employment Details<span> (Last three Organizations)</span></h5>
                    </div>
                    <div class="mb-3 col-md-12" style="overflow: auto">
                        <table class="table table-striped" id="indent_item_table">
                            <thead>
                                <tr style="border-top: 1px solid #DADEE3;">
                                    <th class="vertically-middle" rowspan="2">Sr. No.</th>
                                    <th class="vertically-middle" rowspan="2">Organization</th>
                                    <th class="vertically-middle" rowspan="2">Designation</th>
                                    <th colspan="2" class="center-align">Period of Service</th>
                                    <th class="vertically-middle" rowspan="2">Full Time / Part Time</th>
                                </tr>
                                <tr>
                                    <td >From</td>
                                    <td>To</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1.</td>
                                    <td>
                                        <input type="text" class="form-control" name="organizational_details[0][organization]" value="{{ old("organizational_details[0][organization]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="organizational_details[0][designation]" value="{{ old("organizational_details[0][designation]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="organizational_details[0][from]" value="{{ old("organizational_details[0][from]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="organizational_details[0][to]" value="{{ old("organizational_details[0][to]") }}"></td>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="organizational_details[0][type]" value="{{ old("organizational_details[0][type]") }}"></td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>
                                        <input type="text" class="form-control" name="organizational_details[1][organization]" value="{{ old("organizational_details[1][organization]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="organizational_details[1][designation]" value="{{ old("organizational_details[1][designation]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="organizational_details[1][from]" value="{{ old("organizational_details[1][from]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="organizational_details[1][to]" value="{{ old("organizational_details[1][to]") }}"></td>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="organizational_details[1][type]" value="{{ old("organizational_details[1][type]") }}"></td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3.</td>
                                    <td>
                                        <input type="text" class="form-control" name="organizational_details[2][organization]" value="{{ old("organizational_details[2][organization]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="organizational_details[2][designation]" value="{{ old("organizational_details[2][designation]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="organizational_details[2][from]" value="{{ old("organizational_details[2][from]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="organizational_details[2][to]" value="{{ old("organizational_details[2][to]") }}"></td>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="organizational_details[2][type]" value="{{ old("organizational_details[2][type]") }}"></td>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr class="my-4">
                    <div class="mb-3 col-md-12 my-4">
                        <h5 class="mb-0">Family Details</h5>
                    </div>
                    <div class="mb-3 col-md-12" style="overflow: auto">
                        <table class="table table-striped" id="indent_item_table">
                            <thead>
                                <tr style="border-top: 1px solid #DADEE3;">
                                    <th>Sr. No.</th>
                                    <th>Name</th>
                                    <th>Relationship with Employee</th>
                                    <th>Contact Number</th>
                                    <th>Date of Birth</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1.</td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[0][name]" value="{{ old("family_details[0][name]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[0][relationship]" value="{{ old("family_details[0][relationship]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[0][contact_number]" value="{{ old("family_details[0][contact_number]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[0][dob]" value="{{ old("family_details[0][dob]") }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[1][name]" value="{{ old("family_details[1][name]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[1][relationship]" value="{{ old("family_details[1][relationship]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[1][contact_number]" value="{{ old("family_details[1][contact_number]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[1][dob]" value="{{ old("family_details[1][dob]") }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>3.</td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[2][name]" value="{{ old("family_details[2][name]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[2][relationship]" value="{{ old("family_details[2][relationship]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[2][contact_number]" value="{{ old("family_details[2][contact_number]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[2][dob]" value="{{ old("family_details[2][dob]") }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>4.</td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[3][name]" value="{{ old("family_details[3][name]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[3][relationship]" value="{{ old("family_details[3][relationship]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[3][contact_number]" value="{{ old("family_details[3][contact_number]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[3][dob]" value="{{ old("family_details[3][dob]") }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>5.</td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[4][name]" value="{{ old("family_details[4][name]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[4][relationship]" value="{{ old("family_details[4][relationship]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[4][contact_number]" value="{{ old("family_details[4][contact_number]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="family_details[4][dob]" value="{{ old("family_details[4][dob]") }}">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr class="my-4">
                    <div class="mb-3 col-md-12 my-4">
                        <h5 class="mb-0">Professional References<span> (only reporting manager, superiors & Sr. Colleagues are allowed)</span></h5>
                    </div>
                    <div class="mb-3 col-md-12" style="overflow: auto">
                        <table class="table table-striped" id="indent_item_table">
                            <thead>
                                <tr style="border-top: 1px solid #DADEE3;">
                                    <th>Name</th>
                                    <th>Organization</th>
                                    <th>Designation</th>
                                    <th>Contact No</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" name="professional_details[0][name]" value="{{ old("professional_details[0][name]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="professional_details[0][organization]" value="{{ old("professional_details[0][organization]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="professional_details[0][designation]" value="{{ old("professional_details[0][designation]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="professional_details[0][contact_no]" value="{{ old("professional_details[0][contact_no]") }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" name="professional_details[1][name]" value="{{ old("professional_details[1][name]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="professional_details[1][organization]" value="{{ old("professional_details[1][organization]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="professional_details[1][designation]" value="{{ old("professional_details[1][designation]") }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="professional_details[1][contact_no]" value="{{ old("professional_details[1][contact_no]") }}">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr class="my-4">
                    <div class="mb-3 col-md-12 my-4">
                        <h5 class="mb-0">Bank Account Details</h5>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label" for="bank_name">Bank Name<span class="start-mark">*</span></label>
                        <input type="text" name="bank_name" class="form-control" id="bank_name"  value="{{ old('bank_name') }}" required/>
                        @error('bank_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label" for="branch_name">Branch Name<span class="start-mark">*</span></label>
                        <input type="text" name="branch_name" class="form-control" id="branch_name"  value="{{ old('branch_name') }}" required/>
                        @error('branch_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label" for="branch_name">Account Number<span class="start-mark">*</span></label>
                        <input type="text" name="account_number" class="form-control" id="account_number"  value="{{ old('account_number') }}" required/>
                        @error('account_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label" for="ifsc">IFSC Code<span class="start-mark">*</span></label>
                        <input type="text" name="ifsc" class="form-control" id="ifsc"  value="{{ old('ifsc') }}" required/>
                        @error('ifsc')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <hr class="my-4">
                    <div class="mb-3 col-md-12 my-4">
                        <h5 class="mb-0">General Information</h5>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label class="form-label" for="suffered_from_disease">Have you ever suffered Or suffering from any physical impairment, disease or mental illness? If Yes Give Detail -</label>
                        <textarea name="suffered_from_disease" class="form-control" id="suffered_from_disease" >{{ old('suffered_from_disease') }}</textarea>
                        @error('suffered_from_disease')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                        <label class="form-label" for="practitioner_details">Provide general practitioner details (Name & Contact Number) If Any-</label>
                        <textarea name="practitioner_details" class="form-control" id="practitioner_details" >{{ old('practitioner_details') }}</textarea>
                        @error('practitioner_details')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                        <label class="form-label" for="convicted_in_law">Have you ever been convicted in a court of law? If yes Give Details -</label>
                        <textarea name="convicted_in_law" class="form-control" id="convicted_in_law" >{{ old('convicted_in_law') }}</textarea>
                        @error('convicted_in_law')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
@section('script')
<!-- Page JS -->
<script src="{{ asset("assets/js/pages-account-settings-account.js") }}"></script>
@endsection

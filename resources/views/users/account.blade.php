@extends('layouts.app')

@section('content')
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Account</h4>

    <div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <form id="formAccountSettings" method="POST" action="{{ route('update_profile') }}" enctype="multipart/form-data">
                @csrf
                <h5 class="card-header">Profile Details - {{ auth()->user()->employee_code }}</h5>
                <!-- Account -->
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                    <img
                        src="{{ (auth()->user()->photo != "") ? url('storage/app/'.auth()->user()->photo) : asset("assets/img/avatars/profile.png") }}"
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
                <hr class="my-0" />
                <div class="card-body">

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input class="form-control" type="text" id="name" name="name" value="{{ (old('name') != "") ? old('name') : auth()->user()->name }}" @error('name') autofocus @enderror />
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input class="form-control" type="text" id="email" name="email" value="{{ (old('email') != "") ? old('email') : auth()->user()->email }}" placeholder="john.doe@example.com" @error('email') autofocus @enderror readonly />
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="gender" class="form-label">Gender</label>
                                <select id="gender" name="gender" class="select2 form-select" @error('gender') autofocus @enderror>
                                    @php
                                        $genders = config('constants.GENDER_OPTIONS');
                                    @endphp
                                    <option value="">Select Gender</option>
                                    @foreach ($genders as $key => $gender)
                                        <option value="{{ $key }}" {{ ($key == ((old('gender') != "") ? old('gender') : auth()->user()->gender ) ? 'selected' : '') }}>{{ $gender }}</option>
                                    @endforeach
                                </select>
                                @error('gender')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="dob" class="form-label">Date Of Birth</label>
                                <input class="form-control" type="date" value="{{ (old('dob') != "") ? old('dob') : auth()->user()->dob }}" id="dob" name="dob" @error('dob') autofocus @enderror />
                                @error('dob')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2">Save changes</button>
                            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                        </div>
                </div>
            </form>
            <!-- /Account -->
        </div>
        <div class="card mb-4">
            <h5 class="card-header">Work Information</h5>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <table class="table table-borderless col-md-6">
                            <tr>
                                <th>Entity: </th>
                                <td>{{ (!empty(auth()->user()->entity) ? auth()->user()->entity : '') }}</td>
                            </tr>
                            <tr>
                                <th>Reporting To: </th>
                                <td>{{ (isset(auth()->user()->reportingTo->name) ? auth()->user()->reportingTo->name : '-') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless col-md-6">
                            <tr>
                                <th>Location: </th>
                                <td>{{ (isset(auth()->user()->location->name) ? auth()->user()->location->name : '-') }}</td>
                            </tr>
                            <tr>
                                <th>Department: </th>
                                <td>{{ (isset(auth()->user()->department->name) ? auth()->user()->department->name : '-') }}</td>
                            </tr>
                            <tr>
                                <th>Designation: </th>
                                <td>{{ (isset(auth()->user()->designation->name) ? auth()->user()->designation->name : '-') }}</td>
                            </tr>
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

@section('script')
<!-- Page JS -->
<script src="{{ asset("assets/js/pages-account-settings-account.js") }}"></script>

@endsection

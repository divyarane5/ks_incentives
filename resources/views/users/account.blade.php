@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Account Settings /</span> Account
    </h4>

    <div class="row">

        <!-- ================= PROFILE CARD ================= -->
        <div class="col-md-12">
            <div class="card mb-4">

                <form method="POST" action="{{ route('update_profile') }}" enctype="multipart/form-data">
                    @csrf

                    <h5 class="card-header">
                        Profile Details - {{ $user->employee_code }}
                    </h5>

                    <div class="card-body">

                        <div class="d-flex align-items-center gap-4 mb-4">
                            <img
                                src="{{ ($user->photo != "") ? url('storage/app/'.$user->photo) : asset("assets/img/avatars/profile.png") }}"
                                class="rounded"
                                height="100"
                                width="100"
                            />

                            <div>
                                <input type="file" name="photo" class="form-control mb-2">
                                <small class="text-muted">JPG, JPEG, PNG (Max 800KB)</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $user->name) }}">
                            </div>

                            <div class="mb-3 col-md-6">
                                <label>Email</label>
                                <input type="text" class="form-control"
                                    value="{{ $user->email }}" readonly>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label>Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">Select</option>
                                    @foreach(config('constants.GENDER_OPTIONS') as $key => $gender)
                                        <option value="{{ $key }}"
                                            {{ $key == old('gender', $user->gender) ? 'selected' : '' }}>
                                            {{ $gender }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label>Date of Birth</label>
                                <input type="date" name="dob" class="form-control"
                                    value="{{ old('dob', $user->dob) }}">
                            </div>
                        </div>

                        <button class="btn btn-primary">Save Changes</button>

                    </div>
                </form>
            </div>
        </div>


        <!-- ================= PASSWORD CARD ================= -->
        <div class="col-md-6">
            <div class="card">

                <h5 class="card-header">Change Password</h5>

                <div class="card-body">
                    <form method="POST" action="{{ route('update_password') }}">
                        @csrf

                        <div class="mb-3">
                            <label>Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control" required>
                        </div>

                        <button class="btn btn-primary">Update Password</button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection
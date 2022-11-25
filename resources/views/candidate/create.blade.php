@extends('layouts.app')

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><a href="{{ route('candidate.index') }}" class="text-muted fw-light">Candidate</a>/ Add Candidate</h4>

    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Candidate</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('candidate.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('candidate.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">Name <span class="start-mark">*</span></label>
                        <input name="name" class="form-control" id="name" value="{{ old('name') }}" required />
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="email">Email <span class="start-mark">*</span></label>
                        <input name="email" class="form-control" id="email" value="{{ old('email') }}" required />
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="phone">Phone<span class="start-mark">*</span></label>
                        <input name="phone" class="form-control" id="phone" value="{{ old('phone') }}" required />
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="phone">Designation</label>
                        <input name="designation" class="form-control" id="designation" value="{{ old('designation') }}"  />
                        @error('designation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="phone">Date Of Joining</label>
                        <input type="date" name="joining_date" class="form-control" id="joining_date" value="{{ old('joining_date') }}"  />
                        @error('joining_date')
                            <span class="inva   lid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="entity">Company<span class="start-mark">*</span></label>
                        <select id="entity" name="entity" class="" @error('entity') autofocus @enderror required>
                            <option value="">Select Company</option>
                            @php
                                $companies = config('constants.COMPANY_OPTIONS');
                            @endphp
                            @foreach ($companies as $key => $company)
                                <option value="{{ $company }}" {{ ($company == old('entity') ? 'selected' : '') }}>{{ $company }}</option>
                            @endforeach
                        </select>
                        @error('entity')
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
<!-- / Content -->
@endsection

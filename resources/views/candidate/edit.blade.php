@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4 col-md-6"><a href="{{ route('candidate.index') }}" class="text-muted fw-light">Candidate </a>/ Edit</h4>
    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Candidate</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('candidate.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('candidate.update', $id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            <input type="hidden" name="id" value="{{ $id }}">
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">Name</label>
                        <input name="name" class="form-control" id="name" value="{{ (old('name') != "") ? old('name') : $candidate->name }}" />
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input name="email" class="form-control" id="email" value="{{ (old('email') != "") ? old('email') : $candidate->email }}" />
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="phone">Email</label>
                        <input name="phone" class="form-control" id="phone" value="{{ (old('phone') != "") ? old('phone') : $candidate->phone }}" />
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="phone">Designation</label>
                        <input name="designation" class="form-control" id="designation" value="{{ (old('designation') != "") ? old('designation') : $candidate->designation }}"  />
                        @error('designation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="phone">Date Of Joining</label>
                        <input type="date" name="joining_date" class="form-control" id="joining_date" value="{{ (old('joining_date') != "") ? old('joining_date') : $candidate->joining_date }}"  />
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
                                <option value="{{ $company }}" {{ ($company == ((old('entity') != "") ? old('entity') : $candidate->entity) ? 'selected' : '') }}>{{ $company }}</option>
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
@endsection

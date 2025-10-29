@extends('layouts.app')

@section('content')
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <a href="{{ route('business_unit.index') }}" class="text-muted fw-light">Business Unit</a> / Edit Business Unit
    </h4>

    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Business Unit</h5>
            <small class="text-muted float-end">
                <a class="btn btn-primary" href="{{ route('business_unit.index') }}"> Back</a>
            </small>
        </div>

        <form action="{{ route('business_unit.update', $businessUnit->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">

                    <!-- Name -->
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">Name <span class="start-mark">*</span></label>
                        <input name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $businessUnit->name) }}" required/>
                        @error('name')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Code -->
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="code">Code</label>
                        <input name="code" class="form-control @error('code') is-invalid @enderror" id="code" value="{{ old('code', $businessUnit->code) }}" placeholder="Short code e.g. KS"/>
                        @error('code')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Domain -->
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="domain">Domain</label>
                        <input name="domain" class="form-control @error('domain') is-invalid @enderror" id="domain" value="{{ old('domain', $businessUnit->domain) }}" placeholder="e.g., ks.portal.com"/>
                        @error('domain')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Logo Upload -->
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="logo">Logo <span class="start-mark">*</span></label>
                        @if($businessUnit->logo_path)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $businessUnit->logo_path) }}" alt="Logo" width="100">
                            </div>
                        @endif
                        <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" id="logo" accept="image/*"/>
                        @error('logo')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Theme Color -->
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="theme_color">Theme Color</label>
                        <input type="color" name="theme_color" class="form-control form-control-color @error('theme_color') is-invalid @enderror" id="theme_color" value="{{ old('theme_color', $businessUnit->theme_color ?? '#1e40af') }}"/>
                        @error('theme_color')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Secondary Color -->
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="secondary_color">Secondary Color</label>
                        <input type="color" name="secondary_color" class="form-control form-control-color @error('secondary_color') is-invalid @enderror" id="secondary_color" value="{{ old('secondary_color', $businessUnit->secondary_color ?? '#f0f4f8') }}"/>
                        @error('secondary_color')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Background Image -->
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="background">Background Image</label>
                        @if($businessUnit->background_path)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $businessUnit->background_path) }}" alt="Background" width="150">
                            </div>
                        @endif
                        <input type="file" name="background" class="form-control @error('background') is-invalid @enderror" id="background" accept="image/*"/>
                        @error('background')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Favicon -->
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="favicon">Favicon</label>
                        @if($businessUnit->favicon_path)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $businessUnit->favicon_path) }}" alt="Favicon" width="50">
                            </div>
                        @endif
                        <input type="file" name="favicon" class="form-control @error('favicon') is-invalid @enderror" id="favicon" accept="image/*"/>
                        @error('favicon')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="status">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" id="status">
                            <option value="1" {{ old('status', $businessUnit->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $businessUnit->status) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-3 col-12">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
<!-- / Content -->
@endsection

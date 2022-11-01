@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4 col-md-6"><a href="{{ route('location.index') }}" class="text-muted fw-light">Location /</a> Edit</h4>
    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Location</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('location.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('location.update', $id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            @form_hidden('id', $id)
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">Name<span class="start-mark">*</span></label>
                        <input name="name" class="form-control" id="name" value="{{ (old('name') != "") ? old('name') : $location->name }}" required />
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary" {{ (strtolower($location->name) == 'superadmin') ? 'disabled' : '' }}>Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

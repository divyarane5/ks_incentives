@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4 col-md-6"><a href="{{ route('vendor.index') }}" class="text-muted fw-light">Vendor </a>/ Edit</h4>
    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Vendor</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('vendor.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('vendor.update', $id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            <input type="hidden" name="id" value="{{ $id }}">
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">Name</label>
                        <input name="name" class="form-control" id="name" value="{{ (old('name') != "") ? old('name') : $vendor->name }}" />
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="tds_percentage">TDS in precentage</label>
                        <input type="text" name="tds_percentage" class="form-control" id="tds_percentage" value="{{ (old('tds_percentage') != "") ? old('tds_percentage') : $vendor->tds_percentage }}" onkeyup="validateTwoDecimal(this);" />
                        @error('tds_percentage')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary" {{ (strtolower($vendor->name) == 'superadmin') ? 'disabled' : '' }}>Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

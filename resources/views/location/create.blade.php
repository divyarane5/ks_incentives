@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <a href="{{ route('location.index') }}" class="text-muted fw-light">Location</a> / Add Location
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Location</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('location.index') }}">Back</a></small>
        </div>

        <form action="{{ route('location.store') }}" method="POST" enctype="multipart/form-data"> 
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="city">City <span class="text-danger">*</span></label>
                        <input name="city" class="form-control" id="city" value="{{ old('city') }}" required/>
                        @error('city')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="locality">Locality</label>
                        <input name="locality" class="form-control" id="locality" value="{{ old('locality') }}"/>
                        @error('locality')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>

@endsection

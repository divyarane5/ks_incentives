@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <a href="{{ route('users.index') }}" class="text-muted fw-light">Users</a> / Import Users
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Import User Data</h5>
            <a href="{{ route('users.import.template') }}" class="btn btn-sm btn-outline-primary">
                <i class="bx bx-download"></i> Download Template
            </a>
        </div>

        <div class="card-body">
            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Error Messages --}}
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Import Form --}}
            <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="file">Upload File</label>
                    <div class="col-sm-10">
                        <input 
                            type="file" 
                            name="file" 
                            id="file"
                            class="form-control" 
                            accept=".xlsx,.xls,.csv" 
                            required
                        >
                        <div class="form-text">Supported formats: .xlsx,.xls,.csv</div>
                    </div>
                </div>

                <div class="row justify-content-end">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-upload"></i> Import
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="bx bx-arrow-back"></i> Back
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

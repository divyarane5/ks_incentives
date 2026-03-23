@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <h3>Upload Employees Excel</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    @if(session('import_errors'))
        <div class="alert alert-danger">
            <ul>
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="file">Select Excel File</label>
            <input type="file" name="file" class="form-control" required accept=".xlsx,.xls">
        </div>

        <button class="btn btn-primary">Upload & Import</button>
    </form>
</div>
@endsection
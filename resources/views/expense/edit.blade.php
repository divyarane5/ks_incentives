@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4 col-md-6"><a href="{{ route('expense.index') }}" class="text-muted fw-light">Expense </a>/ Edit</h4>
    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Expense</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('expense.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('expense.update', $id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            <input type="hidden" name="id" value="{{ $id }}">
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="name">Name</label>
                        <input name="name" class="form-control" id="name" value="{{ (old('name') != "") ? old('name') : $expense->name }}" />
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="vendors" class="form-label">Vendors</label>
                        <select name="vendors[]" multiple class="" id="vendors" aria-label="Vendors">
                          <option value="" >Select Vendors</option>
                          @if(!empty($vendors))
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ in_array($vendor->id, $expenseVendors) ? 'selected' : '' }}>{{ $vendor->name }}</option>
                            @endforeach
                          @endif
                        </select>
                        @error('vendors')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary" {{ (strtolower($expense->name) == 'superadmin') ? 'disabled' : '' }}>Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

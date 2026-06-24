@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <a href="{{ route('company-bank.index') }}" class="text-muted fw-light">Company Bank</a> / Add
    </h4>

    <div class="card">

        <div class="card-header d-flex justify-content-between">
            <h5>Add Bank Account</h5>
            <a href="{{ route('company-bank.index') }}" class="btn btn-primary">Back</a>
        </div>

        <form method="POST" action="{{ route('company-bank.store') }}">
            @csrf

            <div class="card-body row">

                <div class="mb-3 col-md-6">
                    <label>Account Name *</label>
                    <input type="text" name="account_name" class="form-control" required>
                </div>

                <div class="mb-3 col-md-6">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" class="form-control">
                </div>

                <div class="mb-3 col-md-6">
                    <label>Account Number</label>
                    <input type="text" name="account_number" class="form-control">
                </div>

                <div class="mb-3 col-md-6">
                    <label>IFSC</label>
                    <input type="text" name="ifsc" class="form-control">
                </div>

                <div class="mb-3 col-md-6">
                    <label>GSTIN</label>
                    <input type="text" name="gstin" class="form-control">
                </div>

                <div class="mb-3 col-md-6">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary">Submit</button>
                </div>

            </div>
        </form>

    </div>
</div>

@endsection
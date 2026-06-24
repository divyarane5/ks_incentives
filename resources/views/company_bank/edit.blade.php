@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <a href="{{ route('company-bank.index') }}" class="text-muted fw-light">Company Bank</a> / Edit
    </h4>

    <div class="card">

        <div class="card-header d-flex justify-content-between">
            <h5>Edit Bank Account</h5>
            <a href="{{ route('company-bank.index') }}" class="btn btn-primary">Back</a>
        </div>

        <form method="POST" action="{{ route('company-bank.update', $account->id) }}">
            @csrf
            @method('PUT')

            <div class="card-body row">

                <div class="mb-3 col-md-6">
                    <label>Account Name *</label>
                    <input type="text" name="account_name"
                           value="{{ $account->account_name }}"
                           class="form-control" required>
                </div>

                <div class="mb-3 col-md-6">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name"
                           value="{{ $account->bank_name }}"
                           class="form-control">
                </div>

                <div class="mb-3 col-md-6">
                    <label>Account Number</label>
                    <input type="text" name="account_number"
                           value="{{ $account->account_number }}"
                           class="form-control">
                </div>

                <div class="mb-3 col-md-6">
                    <label>IFSC</label>
                    <input type="text" name="ifsc"
                           value="{{ $account->ifsc }}"
                           class="form-control">
                </div>

                <div class="mb-3 col-md-6">
                    <label>GSTIN</label>
                    <input type="text" name="gstin"
                           value="{{ $account->gstin }}"
                           class="form-control">
                </div>

                <div class="mb-3 col-md-6">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ $account->status ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$account->status ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary">Update</button>
                </div>

            </div>
        </form>

    </div>
</div>

@endsection
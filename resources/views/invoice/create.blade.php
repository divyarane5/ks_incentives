@extends('layouts.app')

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><a href="{{ route('invoice.index') }}" class="text-muted fw-light">Invoice</a>/ Add Invoice Details</h4>

    <!-- Basic Layout -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Invoice Details</h5>
            <small class="text-muted float-end"><a class="btn btn-primary" href="{{ route('invoice.index') }}"> Back</a></small>
        </div>

        <form action="{{ route('invoice.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                <div class="mb-3 col-md-6">
                        <label for="booking_id" class="form-label">Booking </label>
                        <select name="booking_id"  class="" id="booking_id" aria-label="User" required>
                          <option value="" >Select Booking</option>
                          @if(!empty($bookings))
                            @foreach ($bookings as $booking)
                                <option value="{{ $booking->id }}" {{ (old('booking_id') == $booking->id) ? 'selected' : '' }}>{{ $booking->client_name }} - {{ $booking->name }}</option>
                            @endforeach
                          @endif
                        </select>
                        @error('booking_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                    <label for="invoice_date" class="form-label">Invoice Date<span class="start-mark">*</span></label>
                    <input class="form-control" name="invoice_date" type="date" value="{{ old('invoice_date') }}" id="invoice_date" name="invoice_date" @error('invoice_date') autofocus @enderror required />
                    @error('invoice_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="brokerage">Invoice Percent (in %)<span class="start-mark">*</span></label>
                        <input name="invoice_percent" class="form-control" id="invoice_percent" value="{{ old('invoice_percent') }}" required/>
                        @error('invoice_percent')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="brokerage">Invoice Amount <span class="start-mark">*</span></label>
                        <input name="invoice_amount" class="form-control" id="invoice_amount" value="{{ old('invoice_amount') }}" required/>
                        @error('invoice_amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="brokerage">Payment Received <span class="start-mark">*</span></label>
                        <input name="payment_received" class="form-control" id="payment_received" value="{{ old('payment_received') }}"/>
                        @error('payment_received')
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
<!-- / Content -->
@endsection

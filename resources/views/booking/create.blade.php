@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
<h4 class="fw-bold py-3 mb-4">
    <a href="{{ route('booking.index') }}" class="text-muted fw-light">Booking / </a>
    Add Booking
</h4>

<div class="card mb-4">
<div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Add Booking</h5>
    <a class="btn btn-primary" href="{{ route('booking.index') }}">Back</a>
</div>

<form action="{{ route('booking.store') }}" method="POST">
@csrf

<div class="card-body">

<div class="row">

{{-- ================= BOOKING DETAILS ================= --}}
<h6 class="fw-bold mt-2">Booking Details</h6>

<div class="mb-3 col-md-6">
<label class="form-label">Booking Date *</label>
<input class="form-control" name="booking_date" type="date"
value="{{ old('booking_date') }}" required>
</div>

<div class="mb-3 col-md-6">
<label class="form-label">Lead Source</label>
<input name="lead_source" class="form-control"
value="{{ old('lead_source') }}">
</div>

{{-- ================= CLIENT DETAILS ================= --}}
<h6 class="fw-bold mt-3">Client Details</h6>

<div class="mb-3 col-md-6">
<label class="form-label">Client Name *</label>
<input name="client_name" class="form-control"
value="{{ old('client_name') }}" required>
</div>

<div class="mb-3 col-md-6">
<label class="form-label">Client Contact</label>
<input name="client_contact" class="form-control"
value="{{ old('client_contact') }}">
</div>

{{-- ================= PROPERTY DETAILS ================= --}}
<h6 class="fw-bold mt-3">Property Details</h6>

<div class="mb-3 col-md-6">
<label class="form-label">Developer *</label>
<select name="developer_id" class="form-control" required>
<option value="">Select Developer</option>

@foreach ($developer_name as $developer)
<option value="{{ $developer->id }}"
{{ old('developer_id') == $developer->id ? 'selected' : '' }}>
{{ $developer->name }}
</option>
@endforeach

</select>
</div>

<div class="mb-3 col-md-6">
<label class="form-label">Project *</label>
<select name="project_id" class="form-control" required>

<option value="">Select Project</option>

@foreach ($project_name as $project)
<option value="{{ $project->id }}"
{{ old('project_id') == $project->id ? 'selected' : '' }}>
{{ $project->name }}
</option>
@endforeach

</select>
</div>

<div class="mb-3 col-md-4">
<label class="form-label">Tower</label>
<input name="tower" class="form-control"
value="{{ old('tower') }}">
</div>

<div class="mb-3 col-md-4">
<label class="form-label">Wing</label>
<input name="wing" class="form-control"
value="{{ old('wing') }}">
</div>

<div class="mb-3 col-md-4">
<label class="form-label">Flat No</label>
<input name="flat_no" class="form-control"
value="{{ old('flat_no') }}">
</div>

<div class="mb-3 col-md-6">
<label class="form-label">Configuration</label>
<input name="configuration" class="form-control"
value="{{ old('configuration') }}">
</div>

{{-- ================= FINANCIAL DETAILS ================= --}}
<h6 class="fw-bold mt-3">Financial Details</h6>

<div class="mb-3 col-md-6">
<label class="form-label">Booking Amount</label>
<input name="booking_amount" type="number" step="0.01"
class="form-control"
value="{{ old('booking_amount') }}">
</div>

<div class="mb-3 col-md-6">
<label class="form-label">Agreement Value *</label>
<input name="agreement_value" type="number"
step="0.01" class="form-control"
value="{{ old('agreement_value') }}" required>
</div>

<div class="mb-3 col-md-6">
<label class="form-label">Additional Kicker</label>
<input name="additional_kicker"
type="number" step="0.01"
class="form-control"
value="{{ old('additional_kicker') }}">
</div>

<div class="mb-3 col-md-6">
<label class="form-label">Passback</label>
<input name="passback"
type="number"
step="0.01"
class="form-control"
value="{{ old('passback') }}">
</div>

{{-- ================= REGISTRATION & SALES ================= --}}
<h6 class="fw-bold mt-3">Registration & Sales</h6>

<div class="mb-3 col-md-6">
<label class="form-label">Registration Date</label>
<input class="form-control"
name="registration_date"
type="date"
value="{{ old('registration_date') }}">
</div>

<div class="mb-3 col-md-6">
<label class="form-label">Sales Manager *</label>

<select name="sales_user_id"
class="form-control"
required>

<option value="">Select Sales Manager</option>

@foreach ($salesManagers as $manager)

<option value="{{ $manager->id }}"
{{ old('sales_user_id') == $manager->id ? 'selected' : '' }}>

{{ $manager->name }}

</option>

@endforeach

</select>

</div>

{{-- ================= REMARKS ================= --}}
<h6 class="fw-bold mt-3">Remarks</h6>

<div class="mb-3 col-md-12">
<textarea name="remark"
class="form-control"
rows="3">{{ old('remark') }}</textarea>
</div>

<div class="mt-3">
<button type="submit" class="btn btn-primary">
Submit Booking
</button>
</div>

</div>
</div>
</form>
</div>
</div>

@endsection
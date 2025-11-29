@extends('layouts.public')

@section('title','Client Enquiry - Step 1')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.form-card { border-radius:10px; padding:25px; background:#fff; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:20px;}
.step-title{font-weight:600;border-left:4px solid #0d6efd;padding-left:10px;margin-bottom:12px}
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5 bg-light">
                    <h2 class="text-center mb-4 fw-bold">Client Enquiry — Step 1</h2>
                    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
                    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

                    <form method="POST" action="{{ route('client-enquiry.public.storeStep1') }}" class="needs-validation" novalidate>
                        @csrf

                        @php
                            $propertyTypes = ['Residential Flat','Commercial Office'];
                            $purchasePurposes = ['End Use','Investment','Gift'];
                            $fundingSources = ['Self','Loan','Both'];
                        @endphp

                        <div>
                            <div class="step-title">Client Details</div>
                            @if($errors->any())
                                <div class="alert alert-danger">Please fix the errors below.</div>
                            @endif

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="customer_name" class="form-label">Customer Name *</label>
                                    <input id="customer_name" name="customer_name" value="{{ old('customer_name', $step1['customer_name'] ?? '') }}" class="form-control @error('customer_name') is-invalid @enderror" required>
                                    @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="contact_no" class="form-label">Contact No *</label>
                                    <input id="contact_no" type="tel" name="contact_no" value="{{ old('contact_no', $step1['contact_no'] ?? '') }}" class="form-control @error('contact_no') is-invalid @enderror" required>
                                    @error('contact_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="alternate_no" class="form-label">Alternate No</label>
                                    <input id="alternate_no" type="tel" name="alternate_no" value="{{ old('alternate_no', $step1['alternate_no'] ?? '') }}" class="form-control">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input id="email" type="email" name="email" value="{{ old('email', $step1['email'] ?? '') }}" class="form-control @error('email') is-invalid @enderror">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="profession" class="form-label">Profession</label>
                                    <input id="profession" name="profession" value="{{ old('profession', $step1['profession'] ?? '') }}" class="form-control">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input id="company_name" name="company_name" value="{{ old('company_name', $step1['company_name'] ?? '') }}" class="form-control">
                                </div>

                                <div class="col-md-8 mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input id="address" name="address" value="{{ old('address', $step1['address'] ?? '') }}" class="form-control">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="pin_code" class="form-label">Pin Code</label>
                                    <input id="pin_code" type="number" name="pin_code" value="{{ old('pin_code', $step1['pin_code'] ?? '') }}" class="form-control">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="residential_status" class="form-label">Residential Status</label>
                                    <select id="residential_status" name="residential_status" class="form-select">
                                        <option value="">Select</option>
                                        <option value="India" {{ old('residential_status', $step1['residential_status'] ?? '')=='India' ? 'selected' : '' }}>Indian Resident</option>
                                        <option value="NRI" {{ old('residential_status', $step1['residential_status'] ?? '')=='NRI' ? 'selected' : '' }}>NRI</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3" id="nri_country_wrap" style="display: none;">
                                    <label for="nri_country" class="form-label">NRI Country</label>
                                    <input id="nri_country" name="nri_country" value="{{ old('nri_country', $step1['nri_country'] ?? '') }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="step-title">Requirement & Others</div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="property_type" class="form-label">Property Type</label>
                                    <select id="property_type" name="property_type" class="form-select">
                                        <option value="">Select</option>
                                        @foreach($propertyTypes as $type)
                                            <option value="{{ $type }}" {{ old('property_type', $step1['property_type'] ?? '')==$type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="budget" class="form-label">Budget</label>
                                    <input id="budget" name="budget" value="{{ old('budget', $step1['budget'] ?? '') }}" class="form-control">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="purchase_purpose" class="form-label">Purchase Purpose</label>
                                    <select id="purchase_purpose" name="purchase_purpose" class="form-select">
                                        <option value="">Select</option>
                                        @foreach($purchasePurposes as $purpose)
                                            <option value="{{ $purpose }}" {{ old('purchase_purpose', $step1['purchase_purpose'] ?? '')==$purpose ? 'selected' : '' }}>{{ $purpose }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="funding_source" class="form-label">Funding Source</label>
                                    <select id="funding_source" name="funding_source" class="form-select">
                                        <option value="">Select</option>
                                        @foreach($fundingSources as $source)
                                            <option value="{{ $source }}" {{ old('funding_source', $step1['funding_source'] ?? '')==$source ? 'selected' : '' }}>{{ $source }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="presales_id" class="form-label">Presales Executive</label>
                                    <select id="presales_id" name="presales_id" class="form-select">
                                        <option value="">Select</option>
                                        @foreach($managers as $manager)
                                            <option value="{{ $manager->id }}" {{ old('presales_id', $step1['presales_id'] ?? '')==$manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label d-block">Did the team receive the client call?</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="team_call_yes" name="team_call_received" value="1" {{ old('team_call_received', $step1['team_call_received'] ?? '') == '1' ? 'checked' : '' }}>
                                        <label for="team_call_yes" class="form-check-label">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="team_call_no" name="team_call_received" value="0" {{ old('team_call_received', $step1['team_call_received'] ?? '') == '0' ? 'checked' : '' }}>
                                        <label for="team_call_no" class="form-check-label">No</label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="closing_manager_id" class="form-label">Closing Manager</label>
                                    <select id="closing_manager_id" name="closing_manager_id" class="form-select">
                                        <option value="">Select</option>
                                        @foreach($managers as $manager)
                                            <option value="{{ $manager->id }}" {{ old('closing_manager_id', $step1['closing_manager_id'] ?? '')==$manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="feedback" class="form-label">Feedback</label>
                                    <textarea id="feedback" name="feedback" class="form-control">{{ old('feedback', $step1['feedback'] ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Next — Source of Visit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const residentialSelect = document.getElementById('residential_status');
    const nriWrap = document.getElementById('nri_country_wrap');
    const nriInput = document.getElementById('nri_country');

    function toggleNri() {
        const val = residentialSelect.value;
        nriWrap.style.display = (val === 'NRI') ? 'block' : 'none';
        nriInput.required = (val === 'NRI');
    }

    residentialSelect.addEventListener('change', toggleNri);
    toggleNri(); // initial toggle on page load
});
</script>
@endpush

@endsection

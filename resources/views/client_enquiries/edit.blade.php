@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <a href="{{ route('client-enquiries.index') }}" class="text-muted fw-light">Client Enquiries</a> / Edit Enquiry
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Client Enquiry</h5>
            <small class="text-muted float-end">
                <a class="btn btn-primary" href="{{ route('client-enquiries.index') }}">Back</a>
            </small>
        </div>

        <form action="{{ route('client-enquiries.update', $clientEnquiry->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">

                {{-- ================= CLIENT DETAILS ================= --}}
                <h6 class="fw-bold mb-3 text-primary">Client Details</h6>
                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" class="form-control"
                               value="{{ old('customer_name', $clientEnquiry->customer_name) }}" required>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Contact No <span class="text-danger">*</span></label>
                        <input type="text" name="contact_no" class="form-control"
                               value="{{ old('contact_no', $clientEnquiry->contact_no) }}" required>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Alternate No</label>
                        <input type="text" name="alternate_no" class="form-control"
                               value="{{ old('alternate_no', $clientEnquiry->alternate_no) }}">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $clientEnquiry->email) }}">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Profession</label>
                        <input type="text" name="profession" class="form-control"
                               value="{{ old('profession', $clientEnquiry->profession) }}">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-control"
                               value="{{ old('company_name', $clientEnquiry->company_name) }}">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control"
                               value="{{ old('address', $clientEnquiry->address) }}">
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label">Pin Code</label>
                        <input type="text" name="pin_code" class="form-control"
                               value="{{ old('pin_code', $clientEnquiry->pin_code) }}">
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label">Residential Status</label>
                        <select name="residential_status" class="form-select">
                            <option value="">Select</option>
                            <option value="India" {{ old('residential_status', $clientEnquiry->residential_status) == 'India' ? 'selected' : '' }}>India</option>
                            <option value="NRI" {{ old('residential_status', $clientEnquiry->residential_status) == 'NRI' ? 'selected' : '' }}>NRI</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">NRI Country</label>
                        <input type="text" name="nri_country" class="form-control"
                               value="{{ old('nri_country', $clientEnquiry->nri_country) }}">
                    </div>
                </div>

                <hr>

                {{-- ================= REQUIREMENT DETAILS ================= --}}
                <h6 class="fw-bold mb-3 text-primary">Requirement Details</h6>
                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Property Type</label>
                        <select name="property_type" class="form-select">
                            <option value="">Select</option>
                            <option value="Residential Flat" {{ old('property_type', $clientEnquiry->property_type) == 'Residential Flat' ? 'selected' : '' }}>Residential Flat</option>
                            <option value="Commercial Office" {{ old('property_type', $clientEnquiry->property_type) == 'Commercial Office' ? 'selected' : '' }}>Commercial Office</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Budget</label>
                        <input type="text" name="budget" class="form-control"
                               value="{{ old('budget', $clientEnquiry->budget) }}">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Purchase Purpose</label>
                        <select name="purchase_purpose" class="form-select">
                            <option value="">Select</option>
                            <option value="End Use" {{ old('purchase_purpose', $clientEnquiry->purchase_purpose) == 'End Use' ? 'selected' : '' }}>End Use</option>
                            <option value="Investment" {{ old('purchase_purpose', $clientEnquiry->purchase_purpose) == 'Investment' ? 'selected' : '' }}>Investment</option>
                            <option value="Gift" {{ old('purchase_purpose', $clientEnquiry->purchase_purpose) == 'Gift' ? 'selected' : '' }}>Gift</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Funding Source</label>
                        <select name="funding_source" class="form-select">
                            <option value="">Select</option>
                            <option value="Self" {{ old('funding_source', $clientEnquiry->funding_source) == 'Self' ? 'selected' : '' }}>Self</option>
                            <option value="Loan" {{ old('funding_source', $clientEnquiry->funding_source) == 'Loan' ? 'selected' : '' }}>Loan</option>
                            <option value="Both" {{ old('funding_source', $clientEnquiry->funding_source) == 'Both' ? 'selected' : '' }}>Both</option>
                        </select>
                    </div>
                </div>

                <hr>

                {{-- ================= SOURCE OF VISIT ================= --}}
                <h6 class="fw-bold mb-3 text-primary">Source of Visit</h6>

                @php
                    $sourceOptions = [
                        'Reference', 'Channel Partner', 'Website', 'News', 'Paper Ad', 'Hoarding',
                        'Mailers/SMS', 'Online Ad', 'Call Center', 'Walk in', 'Exhibition', 'Insert',
                        'Existing Client', 'Property Portal'
                    ];

                    // Ensure the current source is safely retrieved (old value first, then model value)
                    $currentSource = old('source_of_visit') ?? ($clientEnquiry->source_of_visit ?? '');
                @endphp

                <div class="row mb-3">
                    <div class="col-md-6">
                        <select name="source_of_visit" id="source_of_visit" class="form-select" required>
                            <option value="">Select Source</option>
                            @foreach($sourceOptions as $source)
                                <option value="{{ $source }}" {{ $source == $currentSource ? 'selected' : '' }}>
                                    {{ $source }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>


                {{-- Channel Partner Section --}}
                <div id="channel_partner_section" class="row mb-3" style="display:none;">
                    <div class="col-md-6">
                        <label class="form-label">Channel Partner</label>
                        <select name="channel_partner_id" class="form-select">
                            <option value="">Select</option>
                            @foreach($channelPartners as $partner)
                                <option value="{{ $partner->id }}" {{ old('channel_partner_id', $clientEnquiry->channel_partner_id) == $partner->id ? 'selected' : '' }}>
                                    {{ $partner->firm_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Sourcing Manager</label>
                        <select name="sourcing_manager_id" class="form-select">
                            <option value="">Select</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ old('sourcing_manager_id', $clientEnquiry->sourcing_manager_id) == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Reference Section --}}
                <div id="reference_section" class="row mb-3" style="display:none;">
                    <div class="col-md-6">
                        <label class="form-label">Reference Name</label>
                        <input type="text" name="reference_name" class="form-control"
                               value="{{ old('reference_name', $clientEnquiry->reference_name) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Reference Contact</label>
                        <input type="text" name="reference_contact" class="form-control"
                               value="{{ old('reference_contact', $clientEnquiry->reference_contact) }}">
                    </div>
                </div>

                {{-- Remarks Section --}}
                <div id="remarks_section" class="row mb-3" style="display:none;">
                    <div class="col-md-12">
                        <label class="form-label">Remarks / Note</label>
                        <textarea name="remarks" class="form-control" rows="3">{{ old('remarks', $clientEnquiry->remarks) }}</textarea>
                    </div>
                </div>

                <hr>

                {{-- ================= TEAM CALL RECEIVED ================= --}}
                <h6 class="fw-bold mb-3 text-primary">Team Call Received</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label d-block">Did the team receive the client call?</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="team_call_received" id="teamCallYes" value="1"
                                {{ old('team_call_received', $clientEnquiry->team_call_received) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="teamCallYes">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="team_call_received" id="teamCallNo" value="0"
                                {{ old('team_call_received', $clientEnquiry->team_call_received) == 0 ? 'checked' : '' }}>
                            <label class="form-check-label" for="teamCallNo">No</label>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- ================= CLOSING MANAGER & FEEDBACK ================= --}}
                <h6 class="fw-bold mb-3 text-primary">Closing Manager & Feedback</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Closing Manager</label>
                        <select name="closing_manager_id" class="form-select">
                            <option value="">Select</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ old('closing_manager_id', $clientEnquiry->closing_manager_id) == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Feedback</label>
                        <textarea name="feedback" class="form-control" rows="2">{{ old('feedback', $clientEnquiry->feedback) }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update</button>

            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sourceSelect = document.getElementById('source_of_visit');
    const channelSection = document.getElementById('channel_partner_section');
    const referenceSection = document.getElementById('reference_section');
    const remarksSection = document.getElementById('remarks_section');

    function toggleSections() {
        const value = sourceSelect.value.trim();

        // Show channel partner section if source is 'Channel Partner'
        channelSection.style.display = (value === 'Channel Partner') ? 'flex' : 'none';

        // Show reference section if source is 'Reference'
        referenceSection.style.display = (value === 'Reference') ? 'flex' : 'none';

        // Show remarks for other sources
        const showRemarksFor = [
            'Website', 'News', 'Paper Ad', 'Hoarding', 'Mailers/SMS',
            'Online Ad', 'Call Center', 'Walk in', 'Exhibition',
            'Insert', 'Existing Client', 'Property Portal'
        ];

        if (showRemarksFor.includes(value) || (value && value !== 'Channel Partner' && value !== 'Reference')) {
            remarksSection.style.display = 'flex';
        } else {
            remarksSection.style.display = 'none';
        }
    }

    // Initial check on page load
    toggleSections();

    // Update on change
    sourceSelect.addEventListener('change', toggleSections);
});
</script>
@endsection

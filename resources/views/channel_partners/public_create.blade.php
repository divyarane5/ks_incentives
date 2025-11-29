@extends('layouts.public')

@section('title', 'Become a Channel Partner')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5 bg-light">
                    <h2 class="mb-4 text-center text-primary fw-bold">Become a Channel Partner</h2>

                    @if(session('success'))
                        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('channel-partner.store.public') }}" method="POST" class="needs-validation" novalidate>
                        @csrf

                        <!-- Basic Info -->
                        <h5 class="mb-3 fw-semibold text-secondary">Basic Info</h5>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Firm Name <span class="text-danger">*</span></label>
                                <input type="text" name="firm_name" class="form-control form-control-lg @error('firm_name') is-invalid @enderror" value="{{ old('firm_name') }}" required>
                                @error('firm_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Owner Name <span class="text-danger">*</span></label>
                                <input type="text" name="owner_name" class="form-control form-control-lg @error('owner_name') is-invalid @enderror" value="{{ old('owner_name') }}" required>
                                @error('owner_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Contact</label>
                                <input type="text" name="contact" class="form-control form-control-lg @error('contact') is-invalid @enderror" value="{{ old('contact') }}">
                                @error('contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">RERA Number</label>
                                <input type="text" name="rera_number" class="form-control form-control-lg @error('rera_number') is-invalid @enderror" value="{{ old('rera_number') }}">
                                @error('rera_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Locations -->
                        <h5 class="mb-3 fw-semibold text-secondary">Locations</h5>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Operational Locations</label>
                                <select name="operational_locations[]" id="operational_locations" class="form-select form-select-lg" multiple></select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Office Locations</label>
                                <select name="office_locations[]" id="office_locations" class="form-select form-select-lg" multiple></select>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <h5 class="mb-3 fw-semibold text-secondary">Additional Info</h5>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Sourcing Manager</label>
                                <select name="sourcing_manager" id="sourcing_manager" class="form-select form-select-lg">
                                    <option value="">Select Sourcing Manager</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('sourcing_manager') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Acquisition Channel *</label>
                                <select name="acquisition_channel[]" class="form-select form-select-lg" multiple>
                                    <option value="telecalling" {{ in_array('telecalling', old('acquisition_channel', [])) ? 'selected' : '' }}>Telecalling</option>
                                    <option value="digital" {{ in_array('digital', old('acquisition_channel', [])) ? 'selected' : '' }}>Digital</option>
                                    <option value="reference" {{ in_array('reference', old('acquisition_channel', [])) ? 'selected' : '' }}>Reference</option>
                                    <option value="BTL" {{ in_array('BTL', old('acquisition_channel', [])) ? 'selected' : '' }}>BTL</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">CP Executive</label>
                                <input type="text" name="cp_executive" class="form-control form-control-lg" value="{{ old('cp_executive') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Property Type *</label>
                                <select name="property_type" class="form-select form-select-lg" required>
                                    <option value="">Select</option>
                                    <option value="commercial" {{ old('property_type')=='commercial' ? 'selected' : '' }}>Commercial</option>
                                    <option value="residential" {{ old('property_type')=='residential' ? 'selected' : '' }}>Residential</option>
                                    <option value="both" {{ old('property_type')=='both' ? 'selected' : '' }}>Both</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 mt-3 fw-bold">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Unified input/select style */
    .form-control, 
    .form-select, 
    .select2-container .select2-selection--single,
    .select2-container .select2-selection--multiple {
        border-radius: 0.5rem !important;
        padding: 0.75rem 1rem !important;
        font-size: 1rem;
        line-height: 1.5;
        border: 1px solid #ced4da;
        height: calc(2.25rem + 1.5rem);
    }

    .form-control:focus, 
    .form-select:focus, 
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default .select2-selection--multiple:focus {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
        outline: none;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered,
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        line-height: 1.5 !important;
        padding-left: 0 !important;
    }

    .form-label { font-weight: 600; }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    function initLocationSelect(selector) {
        $(selector).select2({
            placeholder: 'Select or type a location',
            width: '100%',
            tags: true,
            ajax: {
                // url: "{{ route('locations.ajaxSearch') }}",
                url: "/locations/search",
                type: "GET",
                delay: 250,
                data: function(params) { return { name: params.term }; },
                processResults: function(data) {
                    return { results: data.map(item => ({ id: item.id, text: item.name })) };
                }
            },
            createTag: function(params) {
                return { id: params.term, text: params.term, newTag: true };
            }
        });
    }

    initLocationSelect('#operational_locations');
    initLocationSelect('#office_locations');

    // Sourcing Manager (Select2 normal dropdown)
    $('#sourcing_manager').select2({
        placeholder: 'Select Sourcing Manager',
        width: '100%',
        allowClear: true
    });

    // Append new tags as hidden inputs on submit
    $('form').on('submit', function(e) {
        ['#operational_locations', '#office_locations'].forEach(function(selector) {
            let newTags = $(selector).select2('data').filter(tag => tag.newTag);
            newTags.forEach(function(tag) {
                $(e.currentTarget).append(
                    $('<input>').attr('type', 'hidden')
                                .attr('name', 'new_' + $(selector).attr('id') + '[]')
                                .val(tag.text)
                );
            });
        });
    });
});
</script>
@endpush

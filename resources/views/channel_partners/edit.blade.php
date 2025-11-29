@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        Edit Channel Partner
        <a href="{{ route('channel_partners.index') }}" class="btn btn-secondary float-end">Back</a>
    </h4>

    <div class="card mb-4">
        <form action="{{ route('channel_partners.update', $channelPartner->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">

                <!-- Basic Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Firm Name *</label>
                        <input type="text" name="firm_name" class="form-control" value="{{ old('firm_name', $channelPartner->firm_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Owner Name *</label>
                        <input type="text" name="owner_name" class="form-control" value="{{ old('owner_name', $channelPartner->owner_name) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Contact</label>
                        <input type="text" name="contact" class="form-control" value="{{ old('contact', $channelPartner->contact) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">RERA Number</label>
                        <input type="text" name="rera_number" class="form-control" value="{{ old('rera_number', $channelPartner->rera_number) }}">
                    </div>
                </div>

                <!-- Locations -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Operational Locations</label>
                        <select name="operational_locations[]" id="operational_locations" class="form-select raw-select" multiple>
                            @foreach($operationalLocations as $loc)
                                <option value="{{ $loc['id'] }}" selected>{{ $loc['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Office Locations</label>
                        <select name="office_locations[]" id="office_locations" class="form-select raw-select" multiple>
                            @foreach($officeLocations as $loc)
                                <option value="{{ $loc['id'] }}" selected>{{ $loc['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Other Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Sourcing Manager</label>
                        <select name="sourcing_manager" class="form-control">
                            <option value="">Select Sourcing Manager</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $channelPartner->sourcing_manager == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Acquisition Channel *</label>
                        <select name="acquisition_channel[]" class="form-control" multiple>
                            @php
                                $selectedChannels = is_array($channelPartner->acquisition_channel)
                                    ? $channelPartner->acquisition_channel
                                    : json_decode($channelPartner->acquisition_channel, true);
                            @endphp
                            <option value="telecalling" {{ in_array('telecalling', $selectedChannels ?? []) ? 'selected' : '' }}>Telecalling</option>
                            <option value="digital" {{ in_array('digital', $selectedChannels ?? []) ? 'selected' : '' }}>Digital</option>
                            <option value="reference" {{ in_array('reference', $selectedChannels ?? []) ? 'selected' : '' }}>Reference</option>
                            <option value="BTL" {{ in_array('BTL', $selectedChannels ?? []) ? 'selected' : '' }}>BTL</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    
                    <div class="col-md-6">
                        <label class="form-label">CP Executive</label>
                        <input type="text" name="cp_executive" class="form-control" value="{{ old('cp_executive', $channelPartner->cp_executive) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Property Type *</label>
                        <select name="property_type" class="form-select" required>
                            <option value="">Select</option>
                            <option value="commercial" {{ $channelPartner->property_type == 'commercial' ? 'selected' : '' }}>Commercial</option>
                            <option value="residential" {{ $channelPartner->property_type == 'residential' ? 'selected' : '' }}>Residential</option>
                            <option value="both" {{ $channelPartner->property_type == 'both' ? 'selected' : '' }}>Both</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {

    function initLocationSelect(selector) {
        $(selector).select2({
            placeholder: 'Select or type a location',
            width: '100%',
            tags: true,
            ajax: {
                url: "{{ route('locations.ajaxSearch') }}",
                type: "GET",
                delay: 250,
                data: function(params) { return { name: params.term }; },
                processResults: function(data) {
                    return {
                        results: data.map(item => ({ id: item.id, text: item.name }))
                    };
                }
            },
            createTag: function(params) {
                return {
                    id: params.term,
                    text: params.term,
                    newTag: true
                };
            }
        });
    }

    initLocationSelect('#operational_locations');
    initLocationSelect('#office_locations');

    $('form').on('submit', function(e) {
        ['#operational_locations', '#office_locations'].forEach(function(selector) {
            let newTags = $(selector).select2('data').filter(tag => tag.newTag);
            newTags.forEach(function(tag) {
                $(e.currentTarget).append(
                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'new_' + $(selector).attr('id') + '[]')
                        .val(tag.text)
                );
            });
        });
    });
});
</script>
@endsection

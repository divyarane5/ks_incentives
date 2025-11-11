@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        Add Channel Partner
        <a href="{{ route('channel_partners.index') }}" class="btn btn-secondary float-end">Back</a>
    </h4>

    <div class="card mb-4">
        <form action="{{ route('channel_partners.store') }}" method="POST">
            @csrf
            <div class="card-body">

                <!-- Basic Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Firm Name *</label>
                        <input type="text" name="firm_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Owner Name *</label>
                        <input type="text" name="owner_name" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Contact *</label>
                        <input type="text" name="contact" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">RERA Number</label>
                        <input type="text" name="rera_number" class="form-control">
                    </div>
                </div>

                <!-- Locations -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Operational Locations</label>
                        <select name="operational_locations[]" id="operational_locations" class="form-select raw-select" multiple></select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Office Locations</label>
                        <select name="office_locations[]" id="office_locations" class="form-select raw-select" multiple></select>
                    </div>
                </div>

                <!-- Other Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Sourcing Manager</label>
                        <input type="text" name="sourcing_manager" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Acquisition Channel *</label>
                        <select name="acquisition_channel" class="form-select" required>
                            <option value="">Select</option>
                            <option value="telecalling">Telecalling</option>
                            <option value="digital">Digital</option>
                            <option value="reference">Reference</option>
                            <option value="BTL">BTL</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Property Type *</label>
                        <select name="property_type" class="form-select" required>
                            <option value="">Select</option>
                            <option value="commercial">Commercial</option>
                            <option value="residential">Residential</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {

    function initLocationSelect(selector) {
        $(selector).select2({
            placeholder: 'Select or type a location',
            width: '100%',
            tags: true, // allow typing new locations
            ajax: {
                url: "{{ route('locations.ajaxSearch') }}", // search existing locations
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
                    newTag: true // mark as new
                };
            }
        });
    }

    // Initialize both location fields
    initLocationSelect('#operational_locations');
    initLocationSelect('#office_locations');

    // On form submit, add new tags as hidden inputs
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

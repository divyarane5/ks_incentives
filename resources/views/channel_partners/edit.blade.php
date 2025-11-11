@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        Edit Channel Partner
        <a href="{{ route('channel_partners.index') }}" class="btn btn-secondary float-end">Back</a>
    </h4>

```
<div class="card mb-4">
    <form action="{{ route('channel_partners.update', $channelPartner->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card-body">

            <!-- Basic Info -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Firm Name *</label>
                    <input type="text" name="firm_name" class="form-control" value="{{ $channelPartner->firm_name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Owner Name *</label>
                    <input type="text" name="owner_name" class="form-control" value="{{ $channelPartner->owner_name }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Contact *</label>
                    <input type="text" name="contact" class="form-control" value="{{ $channelPartner->contact }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">RERA Number</label>
                    <input type="text" name="rera_number" class="form-control" value="{{ $channelPartner->rera_number }}">
                </div>
            </div>

            <!-- Locations -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Operational Locations</label>
                    <div class="input-group">
                        <select name="operational_locations[]" id="operational_locations" class="form-select" multiple>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}"
                                    {{ in_array($loc->id, $channelPartner->operational_locations ?? []) ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary addBtn" id="addOperationalBtn">+</button>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Office Locations</label>
                    <div class="input-group">
                        <select name="office_locations[]" id="office_locations" class="form-select" multiple>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}"
                                    {{ in_array($loc->id, $channelPartner->office_locations ?? []) ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary addBtn" id="addOfficeBtn">+</button>
                    </div>
                </div>
            </div>

            <!-- Other Info -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Sourcing Manager</label>
                    <input type="text" name="sourcing_manager" class="form-control" value="{{ $channelPartner->sourcing_manager }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Acquisition Channel *</label>
                    <select name="acquisition_channel" class="form-select" required>
                        <option value="">Select</option>
                        <option value="telecalling" {{ $channelPartner->acquisition_channel == 'telecalling' ? 'selected' : '' }}>Telecalling</option>
                        <option value="digital" {{ $channelPartner->acquisition_channel == 'digital' ? 'selected' : '' }}>Digital</option>
                        <option value="reference" {{ $channelPartner->acquisition_channel == 'reference' ? 'selected' : '' }}>Reference</option>
                        <option value="BTL" {{ $channelPartner->acquisition_channel == 'BTL' ? 'selected' : '' }}>BTL</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Property Type *</label>
                    <select name="property_type" class="form-select" required>
                        <option value="">Select</option>
                        <option value="commercial" {{ $channelPartner->property_type == 'commercial' ? 'selected' : '' }}>Commercial</option>
                        <option value="residential" {{ $channelPartner->property_type == 'residential' ? 'selected' : '' }}>Residential</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update</button>
        </div>
    </form>
</div>
```

</div>

<!-- Modal for Add New Location -->

<div class="modal fade" id="addLocationModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Location</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label>City *</label>
            <input type="text" id="newCity" class="form-control">
        </div>
        <div class="form-group">
            <label>Locality</label>
            <input type="text" id="newLocality" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="saveLocationBtn">Add Location</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    let activeDropdown = null;

    $('#operational_locations, #office_locations').select2({
        placeholder: 'Select or add new location',
        width: '100%'
    });

    $('#addOperationalBtn').click(function() {
        activeDropdown = 'operational';
        $('#addLocationModal').modal('show');
    });

    $('#addOfficeBtn').click(function() {
        activeDropdown = 'office';
        $('#addLocationModal').modal('show');
    });

    $('#saveLocationBtn').click(function() {
        let city = $('#newCity').val().trim();
        let locality = $('#newLocality').val().trim();

        if(!city) { alert('City is required'); return; }

        $.ajax({
            url: "{{ route('locations.ajaxStore') }}",
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', city: city, locality: locality },
            success: function(res) {
                const target = activeDropdown === 'operational' ? '#operational_locations' : '#office_locations';
                const newOption = new Option(res.name, res.id, true, true);
                $(target).append(newOption).trigger('change');

                $('#addLocationModal').modal('hide');
                $('#newCity').val('');
                $('#newLocality').val('');
            },
            error: function(err) {
                alert('Error adding location');
                console.error(err);
            }
        });
    });
});
</script>

@endsection

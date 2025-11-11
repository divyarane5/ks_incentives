@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        Edit Mandate Project
        <a href="{{ route('mandate_projects.index') }}" class="btn btn-secondary float-end">Back</a>
    </h4>

```
<div class="card mb-4">
    <form action="{{ route('mandate_projects.update', $mandateProject->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Project Name *</label>
                    <input type="text" name="project_name" class="form-control" required value="{{ old('project_name', $mandateProject->project_name) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Brand Name</label>
                    <input type="text" name="brand_name" class="form-control" value="{{ old('brand_name', $mandateProject->brand_name) }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="{{ old('location', $mandateProject->location) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">RERA Number</label>
                    <input type="text" name="rera_number" class="form-control" value="{{ old('rera_number', $mandateProject->rera_number) }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Property Type *</label>
                    <select name="property_type" class="form-select" required>
                        <option value="">Select</option>
                        <option value="residential" {{ $mandateProject->property_type == 'residential' ? 'selected' : '' }}>Residential</option>
                        <option value="commercial" {{ $mandateProject->property_type == 'commercial' ? 'selected' : '' }}>Commercial</option>
                    </select>
                </div>
            </div>

            <hr>
            <h5>Configurations</h5>
            <div id="configurations-wrapper">
                @foreach($mandateProject->configurations as $config)
                    <div class="row config-row mb-2">
                        <div class="col-md-5">
                            <input type="text" name="configurations[]" class="form-control" placeholder="Config (1BHK, 2BHK)" value="{{ $config->config }}">
                        </div>
                        <div class="col-md-5">
                            <input type="number" name="carpet_areas[]" class="form-control" placeholder="Carpet Area (sqft)" value="{{ $config->carpet_area }}">
                        </div>
                        <div class="col-md-2">
                            <!-- Buttons will be dynamically updated via JS -->
                            <button type="button" class="btn btn-success add-config">+</button>
                        </div>
                    </div>
                @endforeach
                @if($mandateProject->configurations->isEmpty())
                    <div class="row config-row mb-2">
                        <div class="col-md-5">
                            <input type="text" name="configurations[]" class="form-control" placeholder="Config (1BHK, 2BHK)">
                        </div>
                        <div class="col-md-5">
                            <input type="number" name="carpet_areas[]" class="form-control" placeholder="Carpet Area (sqft)">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-success add-config">+</button>
                        </div>
                    </div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update Project</button>
        </div>
    </form>
</div>
```

</div>
@endsection

@section('script')

<script>
$(document).ready(function() {
    const $wrapper = $('#configurations-wrapper');

    function updateButtons() {
        const $rows = $wrapper.find('.config-row');
        $rows.each(function(index) {
            const $btn = $(this).find('button');
            if(index === $rows.length - 1) {
                // Last row gets + button
                $btn.removeClass('btn-danger remove-config')
                    .addClass('btn-success add-config')
                    .text('+');
            } else {
                // Other rows get - button
                $btn.removeClass('btn-success add-config')
                    .addClass('btn-danger remove-config')
                    .text('-');
            }
        });
    }

    // Initial button setup
    updateButtons();

    // Event delegation
    $wrapper.on('click', 'button', function() {
        const $btn = $(this);
        const $row = $btn.closest('.config-row');

        if($btn.hasClass('add-config')) {
            const $clone = $row.clone();
            $clone.find('input').val(''); // clear inputs
            $wrapper.append($clone);
        }

        if($btn.hasClass('remove-config')) {
            $row.remove();
        }

        // Update buttons after every add/remove
        updateButtons();
    });
});
</script>

@endsection

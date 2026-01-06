@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        Add Mandate Project
        <a href="{{ route('mandate_projects.index') }}" class="btn btn-secondary float-end">Back</a>
    </h4>

```
<div class="card mb-4">
    <form action="{{ route('mandate_projects.store') }}" method="POST">
        @csrf
        <div class="card-body">

            <!-- Project Fields -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Project Name *</label>
                    <input type="text" name="project_name" class="form-control" required value="{{ old('project_name') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Brand Name</label>
                    <input type="text" name="brand_name" class="form-control" value="{{ old('brand_name') }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">RERA Number</label>
                    <input type="text" name="rera_number" class="form-control" value="{{ old('rera_number') }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Property Type *</label>
                    <select name="property_type" class="form-select" required>
                        <option value="">Select</option>
                        <option value="residential">Residential</option>
                        <option value="commercial">Commercial</option>
                        <option value="both">Both</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Threshold (%)</label>
                    <input
                        type="number"
                        name="threshold_percentage"
                        class="form-control"
                        step="0.01"
                        min="0"
                        max="100"
                        placeholder="Enter threshold percentage"
                        value="{{ old('threshold_percentage') }}"
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">Brokerage Criteria On *</label>
                    <select name="brokerage_criteria" class="form-select" required>
                        <option value="">Select Criteria</option>

                        <option value="AV"
                            {{ old('brokerage_criteria') == 'AV' ? 'selected' : '' }}>
                            Agreement Value (UCV + OCC + CPC)
                        </option>

                        <option value="UCV_OCC"
                            {{ old('brokerage_criteria') == 'UCV_OCC' ? 'selected' : '' }}>
                            Unit Consideration Value + Other Charges
                        </option>

                        <option value="UCV_CPC"
                            {{ old('brokerage_criteria') == 'UCV_CPC' ? 'selected' : '' }}>
                            Unit Consideration Value + Car Park Charges
                        </option>
                    </select>
                </div>
            </div>

            <hr>
            <h5>Configurations</h5>
            <div id="configurations-wrapper">
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
            </div>

            <button type="submit" class="btn btn-primary mt-3">Submit</button>
        </div>
    </form>
</div>
```

</div>
@endsection

@section('script')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('configurations-wrapper');

    wrapper.addEventListener('click', function(e) {
        const target = e.target;

        // Add new config row
        if(target.classList.contains('add-config')){
            const row = target.closest('.config-row');
            const clone = row.cloneNode(true);

            // Clear input values
            clone.querySelectorAll('input').forEach(input => input.value = '');

            // Change the button in clone to remove button
            const btn = clone.querySelector('button');
            btn.className = 'btn btn-danger remove-config';
            btn.textContent = '-';

            wrapper.appendChild(clone);
        }

        // Remove config row
        if(target.classList.contains('remove-config')){
            const row = target.closest('.config-row');
            row.remove();
        }
    });
});
</script>

@endsection

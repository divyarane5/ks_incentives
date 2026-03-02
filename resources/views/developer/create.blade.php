@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <a href="{{ route('developer.index') }}" class="text-muted fw-light">Developer</a> / Add Developer
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Developer</h5>
            <small>
                <a class="btn btn-primary" href="{{ route('developer.index') }}">Back</a>
            </small>
        </div>

        <form action="{{ route('developer.store') }}" method="POST">
            @csrf

            <div class="card-body">
                <div class="row">

                    {{-- Developer Name --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Developer Name<span class="text-danger">*</span></label>
                        <input name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <hr class="mt-4 mb-3">

                    <h5 class="mb-3">AOP Ladders</h5>

                    <div id="ladder-wrapper">

                        <div class="row ladder-row">

                            {{-- Start Date --}}
                            <div class="mb-3 col-md-2">
                                <label>Start Date<span class="text-danger">*</span></label>
                                <input type="date" name="aop_s_date[]" class="form-control" required>
                            </div>

                            {{-- End Date --}}
                            <div class="mb-3 col-md-2">
                                <label>End Date<span class="text-danger">*</span></label>
                                <input type="date" name="aop_e_date[]" class="form-control" required>
                            </div>

                            {{-- Min AOP --}}
                            <div class="mb-3 col-md-2">
                                <label>Min AOP (Cr)<span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="min_aop[]" class="form-control" required>
                            </div>

                            {{-- Max AOP --}}
                            <div class="mb-3 col-md-2">
                                <label>Max AOP (Cr)<span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="max_aop[]" class="form-control" required>
                            </div>

                            {{-- Ladder --}}
                            <div class="mb-3 col-md-2">
                                <label>Brokerage (%)<span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="ladder[]" class="form-control" required>
                            </div>

                            {{-- Remove --}}
                            <div class="mb-3 col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-ladder">X</button>
                            </div>

                        </div>

                    </div>

                    {{-- Add More Button --}}
                    <div class="mb-3">
                        <button type="button" id="add-ladder" class="btn btn-secondary btn-sm">
                            + Add More
                        </button>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </div>
            </div>

        </form>
    </div>
</div>

{{-- JS --}}
<script>
document.getElementById('add-ladder').addEventListener('click', function () {
    let wrapper = document.getElementById('ladder-wrapper');
    let row = document.querySelector('.ladder-row').cloneNode(true);

    row.querySelectorAll('input').forEach(input => input.value = '');

    wrapper.appendChild(row);
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-ladder')) {
        let rows = document.querySelectorAll('.ladder-row');
        if (rows.length > 1) {
            e.target.closest('.ladder-row').remove();
        }
    }
});
</script>

@endsection
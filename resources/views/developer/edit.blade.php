@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <a href="{{ route('developer.index') }}" class="text-muted fw-light">Developer</a> / Edit
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Developer</h5>
            <small>
                <a class="btn btn-primary" href="{{ route('developer.index') }}">Back</a>
            </small>
        </div>

        <form action="{{ route('developer.update', $developer->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="row">

                    {{-- Developer Name --}}
                    <div class="mb-3 col-md-6">
                        <label>Developer Name<span class="text-danger">*</span></label>
                        <input name="name" class="form-control"
                               value="{{ old('name', $developer->name) }}" required>
                    </div>

                    <hr class="mt-4 mb-3">

                    <h5 class="mb-3">AOP Ladders</h5>

                    <div id="ladder-wrapper">

                        @foreach($developer->ladders as $ladder)

                        <div class="row ladder-row">

                            <div class="mb-3 col-md-2">
                                <label>Start Date</label>
                                <input type="date" name="aop_s_date[]"
                                       value="{{ $ladder->aop_s_date }}"
                                       class="form-control" required>
                            </div>

                            <div class="mb-3 col-md-2">
                                <label>End Date</label>
                                <input type="date" name="aop_e_date[]"
                                       value="{{ $ladder->aop_e_date }}"
                                       class="form-control" required>
                            </div>

                            <div class="mb-3 col-md-2">
                                <label>Min AOP</label>
                                <input type="number" step="0.01" name="min_aop[]"
                                       value="{{ $ladder->min_aop }}"
                                       class="form-control" required>
                            </div>

                            <div class="mb-3 col-md-2">
                                <label>Max AOP</label>
                                <input type="number" step="0.01" name="max_aop[]"
                                       value="{{ $ladder->max_aop }}"
                                       class="form-control" required>
                            </div>

                            <div class="mb-3 col-md-2">
                                <label>Brokerage (%)</label>
                                <input type="number" step="0.01" name="ladder[]"
                                       value="{{ $ladder->ladder }}"
                                       class="form-control" required>
                            </div>

                            <div class="mb-3 col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-ladder">X</button>
                            </div>

                        </div>

                        @endforeach

                    </div>

                    <div class="mb-3">
                        <button type="button" id="add-ladder" class="btn btn-secondary btn-sm">
                            + Add More
                        </button>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

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
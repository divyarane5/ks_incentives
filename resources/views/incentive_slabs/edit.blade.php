@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">

        <div class="card-header">
            <h4>Edit Incentive Slab</h4>
        </div>

        <div class="card-body">

            <form action="{{ route('incentive-slabs.update', $slab->id) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">Financial Year</label>

                        <input type="text"
                               name="financial_year"
                               class="form-control"
                               value="{{ $slab->financial_year }}"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Role</label>

                        <input type="text"
                               name="role"
                               class="form-control"
                               value="{{ $slab->role }}"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Justification Multiplier</label>

                        <input type="number"
                               step="0.01"
                               name="justification_multiplier"
                               class="form-control"
                               value="{{ $slab->justification_multiplier }}"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">From Times</label>

                        <input type="number"
                               step="0.01"
                               name="from_times"
                               class="form-control"
                               value="{{ $slab->from_times }}"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">To Times</label>

                        <input type="number"
                               step="0.01"
                               name="to_times"
                               class="form-control"
                               value="{{ $slab->to_times }}"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Incentive %</label>

                        <input type="number"
                               step="0.01"
                               name="incentive_percent"
                               class="form-control"
                               value="{{ $slab->incentive_percent }}"
                               required>
                    </div>

                </div>

                <div class="mt-4">
                    <button class="btn btn-primary">
                        Update Slab
                    </button>

                    <a href="{{ route('incentive-slabs.index') }}"
                       class="btn btn-secondary">
                        Back
                    </a>
                </div>

            </form>

        </div>

    </div>

</div>

@endsection
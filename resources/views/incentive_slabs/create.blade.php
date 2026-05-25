@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">

        <div class="card-header">
            <h4>Create Incentive Slab</h4>
        </div>

        <div class="card-body">

            <form action="{{ route('incentive-slabs.store') }}"
                  method="POST">

                @csrf

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">Financial Year</label>

                        <input type="text"
                               name="financial_year"
                               class="form-control"
                               value="2025-26"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Role</label>

                        <input type="text"
                               name="role"
                               class="form-control"
                               placeholder="FOS"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Justification Multiplier</label>

                        <input type="number"
                               step="0.01"
                               name="justification_multiplier"
                               class="form-control"
                               value="4"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">From Times</label>

                        <input type="number"
                               step="0.01"
                               name="from_times"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">To Times</label>

                        <input type="number"
                               step="0.01"
                               name="to_times"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Incentive %</label>

                        <input type="number"
                               step="0.01"
                               name="incentive_percent"
                               class="form-control"
                               required>
                    </div>

                </div>

                <div class="mt-4">
                    <button class="btn btn-primary">
                        Save Slab
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
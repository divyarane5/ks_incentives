@extends('layouts.app')

@section('content')

<div class="container-xxl container-p-y">

    <h4 class="fw-bold mb-4">
        Incentive Calculation Preview
    </h4>

    <div class="card mb-4">

        <div class="card-body">

            <form method="POST"
                  action="{{ route('incentives.preview.data') }}">

                @csrf

                <div class="row">

                    <div class="col-md-3">
                        <label class="form-label">
                            Financial Year
                        </label>

                        <select name="financial_year"
                                class="form-select"
                                required>

                            <option value="">
                                Select FY
                            </option>

                            <option value="2025-26">
                                2025-26
                            </option>

                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            Role
                        </label>

                        <select name="role"
                                class="form-select"
                                required>

                            <option value="">
                                Select Role
                            </option>

                            <option value="FOS">
                                FOS
                            </option>

                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            Department
                        </label>

                        <input type="text"
                               class="form-control"
                               value="Sales"
                               readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            Business Unit
                        </label>

                        <input type="text"
                               class="form-control"
                               value="Keystone Real Estate Advisory"
                               readonly>
                    </div>

                </div>

                <div class="mt-4">
                    <button type="submit"
                            class="btn btn-primary">

                        Preview Incentives
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>
@if(isset($calculations))

<form method="POST"
      action="{{ route('incentives.save') }}">

    @csrf

    <input type="hidden"
           name="financial_year"
           value="{{ $fy }}">

    <input type="hidden"
           name="role"
           value="{{ $role }}">

    <div class="card">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h5 class="mb-1">
                        Incentive Preview
                    </h5>

                    <small class="text-muted">
                        FY : {{ $fy ?? '-' }} |
                        Role : {{ $role ?? '-' }}
                    </small>

                </div>

                @if(count($calculations) > 0)

                <button type="submit"
                        class="btn btn-success">

                    Save Incentive Calculations

                </button>

                @endif

            </div>

            <div class="table-responsive">

                <table class="table table-bordered table-striped align-middle">

                    <thead class="table-dark">

                        <tr>
                            <th width="60">#</th>
                            <th>Employee</th>
                            <th class="text-end">Annual Salary</th>
                            <th class="text-end">Collection</th>
                            <th class="text-center">Times</th>
                            <th class="text-center">Slab %</th>
                            <th class="text-end">Justification</th>
                            <th class="text-end">Eligible Amount</th>
                            <th class="text-end">Final Incentive</th>
                            <th width="120">
                                Action
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        @php

                            $totalSalary = 0;
                            $totalCollection = 0;
                            $totalEligible = 0;
                            $totalIncentive = 0;

                        @endphp

                        @forelse($calculations as $key => $row)

                        @php

                            $totalSalary += $row['annual_salary'];
                            $totalCollection += $row['collection'];
                            $totalEligible += $row['eligible_amount'];
                            $totalIncentive += $row['incentive'];

                        @endphp

                        {{-- HIDDEN INPUTS --}}

                        <input type="hidden"
                               name="calculations[{{ $key }}][user_id]"
                               value="{{ $row['user_id'] }}">

                        <input type="hidden"
                               name="calculations[{{ $key }}][annual_salary]"
                               value="{{ $row['annual_salary'] }}">

                        <input type="hidden"
                               name="calculations[{{ $key }}][collection]"
                               value="{{ $row['collection'] }}">

                        <input type="hidden"
                               name="calculations[{{ $key }}][times]"
                               value="{{ $row['times'] }}">

                        <input type="hidden"
                               name="calculations[{{ $key }}][slab_percent]"
                               value="{{ $row['slab_percent'] }}">

                        <input type="hidden"
                               name="calculations[{{ $key }}][justification]"
                               value="{{ $row['justification'] }}">

                        <input type="hidden"
                               name="calculations[{{ $key }}][eligible_amount]"
                               value="{{ $row['eligible_amount'] }}">

                        <input type="hidden"
                               name="calculations[{{ $key }}][incentive]"
                               value="{{ $row['incentive'] }}">

                        <tr>

                            <td>
                                {{ $key + 1 }}
                            </td>

                            <td>

                                <div class="fw-semibold">
                                    {{ $row['name'] }}
                                </div>

                                <small class="text-muted">
                                    ID : {{ $row['user_id'] }}
                                </small>

                            </td>

                            <td class="text-end">
                                ₹ {{ number_format($row['annual_salary'], 2) }}
                            </td>

                            <td class="text-end fw-semibold text-primary">
                                ₹ {{ number_format($row['collection'], 2) }}
                            </td>

                            <td class="text-center">

                                @if($row['times'] >= 4)

                                    <span class="badge bg-success">
                                        {{ number_format($row['times'],2) }}x
                                    </span>

                                @else

                                    <span class="badge bg-danger">
                                        {{ number_format($row['times'],2) }}x
                                    </span>

                                @endif

                            </td>

                            <td class="text-center">

                                <span class="badge bg-info">
                                    {{ $row['slab_percent'] }}%
                                </span>

                            </td>

                            <td class="text-end">
                                ₹ {{ number_format($row['justification'],2) }}
                            </td>

                            <td class="text-end">

                                @if($row['eligible_amount'] > 0)

                                    <span class="text-success fw-semibold">
                                        ₹ {{ number_format($row['eligible_amount'],2) }}
                                    </span>

                                @else

                                    <span class="text-danger">
                                        ₹ 0.00
                                    </span>

                                @endif

                            </td>

                            <td class="text-end fw-bold text-success">

                                ₹ {{ number_format($row['incentive'],2) }}

                            </td>
                            <td>

                                <a href="{{ route('incentives.show', $row['user_id']) }}"
                                class="btn btn-sm btn-primary">

                                    View

                                </a>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="9"
                                class="text-center py-4">

                                No Data Found

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                    @if(count($calculations) > 0)

                    <tfoot class="table-light fw-bold">

                        <tr>

                            <td colspan="2">
                                Total
                            </td>

                            <td class="text-end">
                                ₹ {{ number_format($totalSalary,2) }}
                            </td>

                            <td class="text-end">
                                ₹ {{ number_format($totalCollection,2) }}
                            </td>

                            <td></td>

                            <td></td>

                            <td></td>

                            <td class="text-end">
                                ₹ {{ number_format($totalEligible,2) }}
                            </td>

                            <td class="text-end text-success">
                                ₹ {{ number_format($totalIncentive,2) }}
                            </td>

                        </tr>

                    </tfoot>

                    @endif

                </table>

            </div>

        </div>

    </div>

</form>

@endif
@endsection
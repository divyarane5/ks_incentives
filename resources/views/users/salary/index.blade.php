@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    {{-- HEADER --}}
    <h4 class="fw-bold mb-3">
        Salary – {{ $user->name }} (FY {{ $fy }})
    </h4>

    {{-- EMPLOYEE INFO --}}
    {{-- EMPLOYEE INFO --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">

                <div class="col-md-3">
                    <strong>Employee:</strong><br>
                    {{ $user->name }}
                </div>

                <div class="col-md-3">
                    <strong>Employee Code:</strong><br>
                    {{ $user->employee_code ?? '-' }}
                </div>

                <div class="col-md-3">
                    <strong>Joining Date:</strong><br>
                    {{ $user->joining_date
                        ? \Carbon\Carbon::parse($user->joining_date)->format('d M Y')
                        : '-' }}
                </div>

                <div class="col-md-3">
                    <strong>Confirmation Date:</strong><br>
                    {{ $user->confirm_date
                        ? \Carbon\Carbon::parse($user->confirm_date)->format('d M Y')
                        : 'Not Confirmed' }} ({{ $user->employement_status }})
                </div>

            </div>
        </div>
    </div>


    <form method="POST" action="{{ route('users.salary.store', $user->id) }}">
        @csrf
        <input type="hidden" name="financial_year" value="{{ $fy }}">

        @php
            $netSalary = $user->net_salary ?? 0;
            $ctcGross = $user->current_ctc ?? 0;
        @endphp

        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th width="150">Gross Salary</th>
                            <th width="170">Deductions</th>
                            <th width="150">Net Salary</th>
                            <th width="120">PT</th>
                            <th width="120">PF</th>
                            <th width="150">Total Cost</th>
                            <th>Remarks</th>
                            <th width="120">Status</th>
                        </tr>
                    </thead>

                    <tbody>
            
                        @foreach($months as $m)

                        @php
                            $monthDate = \Carbon\Carbon::create($m['year'], $m['month'], 1);

                            $confirmationDate = $user->confirm_date
                                ? \Carbon\Carbon::parse($user->confirm_date)
                                : null;

                            // PF Logic (Month-wise)
                            if (
                                $confirmationDate &&
                                $monthDate->gte($confirmationDate) &&
                                $user->employment_status === 'confirmed'
                            ) {
                                $pf = ($user->pf_employee ?? 0) + ($user->pf_employer ?? 0);
                            } else {
                                $pf = 0;
                            }

                            // PT Logic
                            if ($user->gender === 'female' && $user->current_ctc < 25000) {
                                $pt = 0;
                            } else {
                                $pt = $user->professional_tax ?? 0;
                            }

                            $grossDisplay = $netSalary + $pt + $pf;
                        @endphp

                       <tr>
                        <td>{{ $m['label'] }}</td>

                        @php
                            $monthDate = \Carbon\Carbon::create($m['year'], $m['month'], 1);

                            $confirmationDate = $user->confirm_date
                                ? \Carbon\Carbon::parse($user->confirm_date)
                                : null;

                            if (
                                $confirmationDate &&
                                $monthDate->gte($confirmationDate) &&
                                $user->employment_status === 'confirmed'
                            ) {
                                $pf = ($user->pf_employee ?? 0) + ($user->pf_employer ?? 0);
                            } else {
                                $pf = 0;
                            }

                            if ($user->gender === 'female' && $user->current_ctc < 25000) {
                                $pt = 0;
                            } else {
                                $pt = $user->professional_tax ?? 0;
                            }

                            $grossDisplay = $netSalary + $pt + $pf;

                            $credited = $m['salary_credited'] ?? 0;
                            $deduction = $m['extra_deduction'] ?? max($netSalary - $credited, 0);
                            $rowTotal = $credited + $pt + $pf;
                        @endphp

                        {{-- Gross --}}
                        <td>
        ₹ {{ number_format($grossDisplay, 2) }}
        <input type="hidden"
            name="salary[{{ $m['year'] }}][{{ $m['month'] }}][gross_salary]"
            value="{{ $grossDisplay }}">
    </td>

    {{-- Deductions --}}
    <td>
        ₹ <span class="deduction-amount">
            {{ number_format($deduction, 2) }}
        </span>
    </td>

    {{-- Net Salary --}}
    <td>
        <input type="number"
            class="form-control salary-input"
            data-standard="{{ $netSalary }}"
            data-pt="{{ $pt }}"
            data-pf="{{ $pf }}"
            name="salary[{{ $m['year'] }}][{{ $m['month'] }}][salary_credited]"
            value="{{ $credited }}"
            {{ !$m['enabled'] ? 'disabled' : '' }}>

        <input type="hidden"
            name="salary[{{ $m['year'] }}][{{ $m['month'] }}][system_net_salary]"
            value="{{ $netSalary }}">
    </td>

    {{-- PT --}}
    <td>
        ₹ {{ number_format($pt, 2) }}
        <input type="hidden"
            name="salary[{{ $m['year'] }}][{{ $m['month'] }}][professional_tax]"
            value="{{ $pt }}">
    </td>

    {{-- PF --}}
    <td>
        ₹ {{ number_format($pf, 2) }}
        <input type="hidden"
            name="salary[{{ $m['year'] }}][{{ $m['month'] }}][pf_amount]"
            value="{{ $pf }}">
    </td>

    {{-- Total Cost --}}
    <td>
        ₹ <span class="row-total">
            {{ number_format($rowTotal, 2) }}
        </span>
        <input type="hidden"
            name="salary[{{ $m['year'] }}][{{ $m['month'] }}][total_employee_cost]"
            value="{{ $rowTotal }}">
    </td>

    {{-- Extra Deduction --}}
    <input type="hidden"
        name="salary[{{ $m['year'] }}][{{ $m['month'] }}][extra_deduction]"
        value="{{ $deduction }}">

    {{-- Remarks --}}
    <td>
        <input type="text"
            class="form-control"
            name="salary[{{ $m['year'] }}][{{ $m['month'] }}][remarks]"
            value="{{ $m['remarks'] ?? '' }}"
            {{ !$m['enabled'] ? 'disabled' : '' }}>
    </td>

    {{-- Status --}}
    <td>
        <select class="form-control"
            name="salary[{{ $m['year'] }}][{{ $m['month'] }}][status]"
            {{ !$m['enabled'] ? 'disabled' : '' }}>
            <option value="Pending"
                {{ $m['status'] == 'Pending' ? 'selected' : '' }}>
                Pending
            </option>
            <option value="Credited"
                {{ $m['status'] == 'Credited' ? 'selected' : '' }}>
                Credited
            </option>
        </select>
    </td>
</tr>


                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr class="fw-bold">
                            <th>Total</th>
                            <th colspan="6">
                                ₹ <span id="salary-total">{{ number_format($total, 2) }}</span>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="mt-3 text-end">
            <button type="submit" class="btn btn-primary">
                Save Salary
            </button>
        </div>
    </form>
</div>
@endsection


@section('script')
<script>
function calculateSalary() {

    let total = 0;

    document.querySelectorAll('.salary-input').forEach(function (input) {

        if (input.disabled) return;

        let standard = parseFloat(input.dataset.standard) || 0;
        let credited = parseFloat(input.value) || 0;
        let pt = parseFloat(input.dataset.pt) || 0;
        let pf = parseFloat(input.dataset.pf) || 0;

        let deduction = standard - credited;
        if (deduction < 0) deduction = 0;


        let row = input.closest('tr');

        row.querySelector('.deduction-amount').innerText =
        deduction.toLocaleString('en-IN', { minimumFractionDigits: 2 });

        // 🔥 THIS LINE IS MISSING
        row.querySelector('input[name*="[extra_deduction]"]').value = deduction;

        let rowTotal = credited + pt + pf;

        row.querySelector('.row-total').innerText =
            rowTotal.toLocaleString('en-IN', { minimumFractionDigits: 2 });

        if (input.value !== '') {
            total += credited;
        }
    });

    document.getElementById('salary-total').innerText =
        total.toLocaleString('en-IN', { minimumFractionDigits: 2 });
}

document.addEventListener('input', function (e) {
    if (e.target.classList.contains('salary-input')) {
        calculateSalary();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    calculateSalary();
});
</script>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    <h4 class="fw-bold mb-3">
        Salary – {{ $user->name }} (FY {{ $fy }})
    </h4>

    <form method="POST" action="{{ route('users.salary.store', $user->id) }}">
        @csrf
        <input type="hidden" name="financial_year" value="{{ $fy }}">

        @php $netSalary = $user->net_salary ?? 0; @endphp

        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="150">Month</th>
                            <th width="150">Gross</th>
                            <th width="150">LOP</th>
                            <th width="180">TDS</th>
                            <th width="180">Net Credited</th>
                            <th width="150">PT</th>
                            <th width="150">PF</th>
                            <th width="150">Total Cost</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($months as $m)

                        @php
                            $monthDate = \Carbon\Carbon::create($m['year'], $m['month'], 1);
                            $confirmationDate = $user->confirm_date
                                ? \Carbon\Carbon::parse($user->confirm_date)
                                : null;

                            $pf = ($confirmationDate &&
                                   $monthDate->gte($confirmationDate) &&
                                   $user->employment_status === 'confirmed')
                                ? ($user->pf_employee ?? 0) + ($user->pf_employer ?? 0)
                                : 0;

                            $pt = ($user->gender === 'female' && $user->current_ctc < 25000)
                                ? 0
                                : ($user->professional_tax ?? 0);

                            $gross = $netSalary + $pt + $pf;

                            $credited = $m['salary_credited'] ?? 0;
                            $tds = $m['tds'] ?? 0;
                            $lop = $m['extra_deduction'] ?? max($netSalary - $credited - $tds, 0);
                            $totalCost = $credited + $tds + $pt + $pf;
                        @endphp

                        <tr>
                            <td>{{ $m['label'] }}</td>

                            <td>₹ {{ number_format($gross,2) }}</td>

                            <td>
                                ₹ <span class="lop-display">
                                    {{ number_format($lop,2) }}
                                </span>

                                <input type="hidden"
                                    name="salary[{{ $m['year'] }}][{{ $m['month'] }}][extra_deduction]"
                                    value="{{ $lop }}">
                            </td>

                            <td>
                                <input type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control tds-input"
                                    name="salary[{{ $m['year'] }}][{{ $m['month'] }}][tds]"
                                    value="{{ $tds }}">
                            </td>

                            <td>
                                <input type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control salary-input"
                                    data-standard="{{ $netSalary }}"
                                    data-pt="{{ $pt }}"
                                    data-pf="{{ $pf }}"
                                    name="salary[{{ $m['year'] }}][{{ $m['month'] }}][salary_credited]"
                                    value="{{ $credited }}"
                                    {{ !$m['enabled'] ? 'disabled' : '' }}>
                            </td>

                            <td>₹ {{ number_format($pt,2) }}</td>
                            <td>₹ {{ number_format($pf,2) }}</td>

                            <td>
                                ₹ <span class="row-total">
                                    {{ number_format($totalCost,2) }}
                                </span>
                            </td>
                        </tr>

                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr class="fw-bold bg-light">
                            <th>Total</th>
                            <th id="gross-total">0.00</th>
                            <th id="lop-total">0.00</th>
                            <th id="tds-total">0.00</th>
                            <th id="net-total">0.00</th>
                            <th id="pt-total">0.00</th>
                            <th id="pf-total">0.00</th>
                            <th id="cost-total">0.00</th>
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

    let grossTotal=0, lopTotal=0, tdsTotal=0,
        netTotal=0, ptTotal=0, pfTotal=0, costTotal=0;

    document.querySelectorAll('tbody tr').forEach(row => {

        let salaryInput = row.querySelector('.salary-input');
        let tdsInput = row.querySelector('.tds-input');
        if (!salaryInput || salaryInput.disabled) return;

        let standard = parseFloat(salaryInput.dataset.standard) || 0;
        let pt = parseFloat(salaryInput.dataset.pt) || 0;
        let pf = parseFloat(salaryInput.dataset.pf) || 0;
        let credited = parseFloat(salaryInput.value) || 0;
        let tds = parseFloat(tdsInput.value) || 0;

        if (credited + tds > standard) {
            tds = Math.max(0, standard - credited);
            tdsInput.value = tds;
        }

        let lop = standard - credited - tds;
        if (lop < 0) lop = 0;

        row.querySelector('.lop-display').innerText =
            lop.toLocaleString('en-IN',{minimumFractionDigits:2});

        row.querySelector('input[name*="[extra_deduction]"]').value = lop;

        let totalCost = credited + tds + pt + pf;

        row.querySelector('.row-total').innerText =
            totalCost.toLocaleString('en-IN',{minimumFractionDigits:2});

        grossTotal += standard + pt + pf;
        lopTotal += lop;
        tdsTotal += tds;
        netTotal += credited;
        ptTotal += pt;
        pfTotal += pf;
        costTotal += totalCost;
    });

    document.getElementById('gross-total').innerText = grossTotal.toFixed(2);
    document.getElementById('lop-total').innerText = lopTotal.toFixed(2);
    document.getElementById('tds-total').innerText = tdsTotal.toFixed(2);
    document.getElementById('net-total').innerText = netTotal.toFixed(2);
    document.getElementById('pt-total').innerText = ptTotal.toFixed(2);
    document.getElementById('pf-total').innerText = pfTotal.toFixed(2);
    document.getElementById('cost-total').innerText = costTotal.toFixed(2);
}

document.addEventListener('input', e=>{
    if(e.target.classList.contains('salary-input') ||
       e.target.classList.contains('tds-input')){
        calculateSalary();
    }
});

document.addEventListener('DOMContentLoaded', calculateSalary);
</script>
@endsection

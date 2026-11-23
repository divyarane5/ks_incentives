@use('App\Support\PeopleFormat', 'F')
@extends('layouts.app')

@section('content')
@include('users.partials.people-styles')
<style>
    .sal-head { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; justify-content: space-between; margin-bottom: 12px; }
    .sal-head h1 { font-size: 1.2rem !important; margin: 0; color: var(--kp-navy); }
    .sal-head .who { font-size: .82rem; color: var(--kp-muted); }
    .sal-fy { display: flex; gap: 8px; align-items: center; }
    .sal-fy select { min-height: 42px; border-radius: 10px; min-width: 130px; }
    .sal-sum { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 14px; }
    .sal-sum .kp-fact .v { font-size: 1.02rem; }
    .sal-sum .kp-fact.main { background: var(--kp-navy); border-color: var(--kp-navy); }
    .sal-sum .kp-fact.main .l, .sal-sum .kp-fact.main .s { color: color-mix(in srgb, #fff 70%, transparent); }
    .sal-sum .kp-fact.main .v { color: #fff; }
    .sal-tools { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; justify-content: space-between; padding: 10px 14px; border-bottom: 1px solid var(--kp-line); }
    .sal-tools .hint { font-size: .78rem; color: var(--kp-muted); }
    .sal-cols, .sal-row { display: grid; gap: 8px; align-items: center; }
    .sal-cols { display: none; }
    .sal-row { grid-template-columns: 1fr 1fr; padding: 12px 14px; border-bottom: 1px solid var(--kp-line); }
    .sal-row:last-child { border-bottom: 0; }
    .sal-row .mon { grid-column: 1 / -1; display: flex; align-items: center; justify-content: space-between; }
    .sal-row .mon b { color: var(--kp-navy); font-size: .98rem; }
    .sal-row label { display: block; font-size: .68rem; font-weight: 700; color: var(--kp-muted); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 2px; }
    .sal-row .form-control { min-height: 42px; border-radius: 9px; font-size: 16px; font-variant-numeric: tabular-nums; text-align: right; }
    .sal-row .calc { min-height: 42px; display: flex; align-items: center; justify-content: flex-end; padding: 0 10px; border-radius: 9px; background: #f6f8fb; font-weight: 600; font-variant-numeric: tabular-nums; }
    .sal-row .calc.lop.has { background: #fff4e0; color: #9a5b00; }
    .sal-row .calc.cost { background: var(--rs-navy-soft, #eef1f6); color: var(--kp-navy); font-weight: 700; }
    .sal-row.empty .mon b::after { content: " · not entered"; font-weight: 500; color: #b0b8c6; font-size: .8rem; }
    .sal-foot { display: none; }
    .sal-bar { position: sticky; bottom: 0; z-index: 6; display: flex; gap: 10px; align-items: center; justify-content: space-between; background: #fff; border: 1px solid var(--kp-line); border-radius: 12px; padding: 10px 14px calc(10px + env(safe-area-inset-bottom)); margin-top: 12px; box-shadow: 0 -4px 14px rgba(16, 24, 40, .06); }
    .sal-bar .t { font-size: .78rem; color: var(--kp-muted); }
    .sal-bar .t b { display: block; color: var(--kp-navy); font-size: 1rem; }
    .sal-bar .btn { min-height: 46px; min-width: 140px; font-weight: 700; border-radius: 10px; }
    @media (min-width: 992px) {
        .sal-sum { grid-template-columns: repeat(4, 1fr); }
        .sal-cols, .sal-row, .sal-foot { grid-template-columns: 110px repeat(7, minmax(0, 1fr)); }
        .sal-cols { display: grid; padding: 10px 14px; border-bottom: 1px solid var(--kp-line); font-size: .7rem; font-weight: 700; color: var(--kp-muted); text-transform: uppercase; letter-spacing: .04em; }
        .sal-cols span:not(:first-child) { text-align: right; }
        .sal-row { padding: 8px 14px; }
        .sal-row .mon { grid-column: auto; display: block; }
        .sal-row .mon .kp-chip { font-size: .62rem; padding: 1px 6px; }
        .sal-row label { display: none; }
        .sal-row.empty .mon b::after { content: ""; }
        .sal-foot { display: grid; gap: 8px; padding: 12px 14px; background: #f6f8fb; border-top: 1px solid var(--kp-line); font-weight: 700; color: var(--kp-navy); font-variant-numeric: tabular-nums; border-radius: 0 0 12px 12px; }
        .sal-foot span:not(:first-child) { text-align: right; }
    }
</style>

@php
    $netSalary = $user->net_salary ?? 0;
    $isFemale = strcasecmp((string) $user->gender, 'female') === 0;
    $joiningDate = $user->joining_date ? \Carbon\Carbon::parse($user->joining_date)->startOfMonth() : null;
    $leavingDate = $user->leaving_date ? \Carbon\Carbon::parse($user->leaving_date)->startOfMonth() : null;
    $confirmationDate = $user->confirm_date ? \Carbon\Carbon::parse($user->confirm_date)->startOfMonth() : null;
@endphp

<div class="container-xxl flex-grow-1 container-p-y kp">
    <div class="kp-crumb">
        <a href="{{ route('users.index') }}">KeyPeople</a> <i class="bx bx-chevron-right"></i>
        <a href="{{ route('users.show', $user->id) }}">{{ $user->first_name ?: $user->name }}</a> <i class="bx bx-chevron-right"></i>
        <span>Salary</span>
    </div>

    <div class="sal-head">
        <div>
            <h1>Monthly salary · FY {{ $fy }}</h1>
            <div class="who">
                {{ $user->name }} · {{ $user->employee_code }}
                · Joined {{ F::date($user->joining_date) ?? '—' }}
                · Confirmed {{ F::date($user->confirm_date) ?? '—' }}
                @if($user->leaving_date) · Left {{ F::date($user->leaving_date) }}@endif
            </div>
        </div>
        <form method="GET" class="sal-fy">
            <label for="fyPick" class="text-muted small mb-0">Year</label>
            <select id="fyPick" name="fy" class="form-select raw-select" onchange="this.form.submit()">
                @foreach($fyOptions as $opt)
                    <option value="{{ $opt }}" {{ $opt === $fy ? 'selected' : '' }}>FY {{ $opt }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="sal-sum">
        <div class="kp-fact main"><div class="l">Total cost to company</div><div class="v kp-money" id="sum-cost">—</div><div class="s" id="sum-months">—</div></div>
        <div class="kp-fact"><div class="l">Net credited</div><div class="v kp-money" id="sum-net">—</div><div class="s">to bank</div></div>
        <div class="kp-fact"><div class="l">TDS + PT + PF</div><div class="v kp-money" id="sum-ded">—</div><div class="s" id="sum-ded-s">—</div></div>
        <div class="kp-fact"><div class="l">Loss of pay</div><div class="v kp-money" id="sum-lop">—</div><div class="s">Standard take-home {{ F::inr($netSalary) ?? '—' }}</div></div>
    </div>

    <form method="POST" action="{{ route('users.salary.store', $user->id) }}" id="salaryForm">
        @csrf
        <input type="hidden" name="financial_year" value="{{ $fy }}">

        <div class="kp-panel">
            <div class="sal-tools">
                <span class="hint">Type gross and net credited. Loss of pay and total cost work themselves out.</span>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillStandard()"><i class="bx bx-magic"></i> Fill empty months with standard salary</button>
            </div>

            <div class="sal-cols">
                <span>Month</span><span>Gross</span><span>Net credited</span><span>TDS</span><span>PT</span><span>PF</span><span>Loss of pay</span><span>Total cost</span>
            </div>

            @foreach($months as $m)
                @php
                    $monthDate = \Carbon\Carbon::create($m['year'], $m['month'], 1);
                    if ($joiningDate && $monthDate->lt($joiningDate)) continue;
                    if ($leavingDate && $monthDate->gt($leavingDate)) continue;

                    if (isset($m['pf_amount']) && $m['pf_amount'] !== null) {
                        $pf = $m['pf_amount'];
                    } else {
                        $pf = ($confirmationDate && $monthDate->gte($confirmationDate) && $user->employment_status === 'confirmed')
                            ? ($user->pf_employee ?? 0) + ($user->pf_employer ?? 0)
                            : 0;
                    }

                    // PT: women earning under ₹25,000 a month pay none; otherwise February is ₹300.
                    if (isset($m['professional_tax']) && $m['professional_tax'] !== null) {
                        $pt = $m['professional_tax'];
                    } elseif ($isFemale && $user->current_ctc < 25000) {
                        $pt = 0;
                    } elseif ($monthDate->month == 2) {
                        $pt = 300;
                    } else {
                        $pt = $user->professional_tax ?? 0;
                    }

                    $credited = $m['salary_credited'] ?? 0;
                    $tds = $m['tds'] ?? 0;
                    $lop = $m['extra_deduction'] ?? 0;
                    $totalCost = $credited + $tds + $pt + $pf;
                    $gross = $totalCost + $lop;
                    $saved = $m['gross_salary'] !== null;
                    $key = "salary[{$m['year']}][{$m['month']}]";
                @endphp
                <div class="sal-row {{ $saved || $credited > 0 ? '' : 'empty' }}" data-std-pt="{{ $pt }}" data-std-pf="{{ $pf }}">
                    <div class="mon">
                        <b>{{ $m['label'] }}</b>
                        @if($saved)<span class="kp-chip ok">Saved</span>@endif
                        @php $ul = $unpaidLeave[$m['year'].'-'.$m['month']] ?? 0; @endphp
                        @if($ul)<span class="kp-chip warn" title="Approved unpaid leave">{{ rtrim(rtrim(number_format($ul, 1), '0'), '.') }}d unpaid</span>@endif
                        @if($saved && $credited > 0)
                            <a class="small ms-1" target="_blank" href="{{ route('payslip.pdf', [$user->id, $fy, $m['month']]) }}">Payslip</a>
                        @endif
                    </div>
                    <div>
                        <label>Gross</label>
                        <input type="number" step="0.01" inputmode="decimal" class="form-control gross-input" name="{{ $key }}[gross]" value="{{ $gross }}">
                    </div>
                    <div>
                        <label>Net credited</label>
                        <input type="number" step="0.01" min="0" inputmode="decimal" class="form-control salary-input"
                               data-standard="{{ $netSalary }}" data-pt="{{ $pt }}" data-pf="{{ $pf }}"
                               name="{{ $key }}[salary_credited]" value="{{ $credited }}" {{ ! $m['enabled'] ? 'disabled' : '' }}>
                    </div>
                    <div>
                        <label>TDS</label>
                        <input type="number" step="0.01" min="0" inputmode="decimal" class="form-control tds-input" name="{{ $key }}[tds]" value="{{ $tds }}">
                    </div>
                    <div>
                        <label>PT</label>
                        <input type="number" step="0.01" min="0" inputmode="decimal" class="form-control pt-input" name="{{ $key }}[pt]" value="{{ $pt }}">
                    </div>
                    <div>
                        <label>PF</label>
                        <input type="number" step="0.01" min="0" inputmode="decimal" class="form-control pf-input" name="{{ $key }}[pf]" value="{{ $pf }}">
                    </div>
                    <div>
                        <label>Loss of pay</label>
                        <div class="calc lop {{ $lop > 0 ? 'has' : '' }}"><span class="lop-display">{{ F::inr($lop, 0, false) }}</span></div>
                        <input type="hidden" class="lop-input" name="{{ $key }}[extra_deduction]" value="{{ $lop }}">
                    </div>
                    <div>
                        <label>Total cost</label>
                        <div class="calc cost"><span class="row-total">{{ F::inr($totalCost, 0, false) }}</span></div>
                    </div>
                </div>
            @endforeach

            <div class="sal-foot">
                <span>Total</span>
                <span id="gross-total">0</span><span id="net-total">0</span><span id="tds-total">0</span>
                <span id="pt-total">0</span><span id="pf-total">0</span><span id="lop-total">0</span><span id="cost-total">0</span>
            </div>
        </div>

        @canany(['user-salary-create', 'user-salary-edit'])
            <div class="sal-bar">
                <div class="t">FY {{ $fy }} total cost<b id="bar-cost">—</b></div>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Save salary</button>
            </div>
        @endcanany
    </form>
</div>
@endsection

@section('script')
<script>
(function () {
    function inr(n) { return (Math.round(n) || 0).toLocaleString('en-IN'); }
    function num(el) { return el ? (parseFloat(el.value) || 0) : 0; }

    function calculateSalary() {
        var t = { gross: 0, lop: 0, tds: 0, net: 0, pt: 0, pf: 0, cost: 0, months: 0 };
        document.querySelectorAll('.sal-row').forEach(function (row) {
            var salary = row.querySelector('.salary-input');
            if (!salary || salary.disabled) return;
            var gross = num(row.querySelector('.gross-input'));
            var credited = num(salary), tds = num(row.querySelector('.tds-input'));
            var pt = num(row.querySelector('.pt-input')), pf = num(row.querySelector('.pf-input'));
            var cost = credited + tds + pt + pf;
            var lop = Math.max(gross - cost, 0);
            row.querySelector('.lop-input').value = lop.toFixed(2);
            row.querySelector('.lop-display').textContent = inr(lop);
            row.querySelector('.lop-display').parentNode.classList.toggle('has', lop > 0);
            row.querySelector('.row-total').textContent = inr(cost);
            row.classList.toggle('empty', credited <= 0 && gross <= 0);
            if (credited > 0) t.months++;
            t.gross += gross; t.lop += lop; t.tds += tds; t.net += credited; t.pt += pt; t.pf += pf; t.cost += cost;
        });
        var set = function (id, v) { var el = document.getElementById(id); if (el) el.textContent = v; };
        set('gross-total', inr(t.gross)); set('lop-total', inr(t.lop)); set('tds-total', inr(t.tds));
        set('net-total', inr(t.net)); set('pt-total', inr(t.pt)); set('pf-total', inr(t.pf)); set('cost-total', inr(t.cost));
        set('sum-cost', '₹ ' + inr(t.cost)); set('bar-cost', '₹ ' + inr(t.cost));
        set('sum-months', t.months + ' month' + (t.months === 1 ? '' : 's') + ' paid');
        set('sum-net', '₹ ' + inr(t.net)); set('sum-lop', '₹ ' + inr(t.lop));
        set('sum-ded', '₹ ' + inr(t.tds + t.pt + t.pf));
        set('sum-ded-s', 'TDS ' + inr(t.tds) + ' · PT ' + inr(t.pt) + ' · PF ' + inr(t.pf));
    }

    // For months with nothing typed: net = standard take-home, gross = net + PT + PF.
    window.fillStandard = function () {
        var filled = 0;
        document.querySelectorAll('.sal-row').forEach(function (row) {
            var salary = row.querySelector('.salary-input');
            if (!salary || salary.disabled || num(salary) > 0) return;
            var std = parseFloat(salary.dataset.standard) || 0;
            if (std <= 0) return;
            var pt = num(row.querySelector('.pt-input')), pf = num(row.querySelector('.pf-input')), tds = num(row.querySelector('.tds-input'));
            salary.value = std.toFixed(2);
            row.querySelector('.gross-input').value = (std + pt + pf + tds).toFixed(2);
            filled++;
        });
        calculateSalary();
        if (!filled) alert('No empty months to fill, or no standard take-home is set on the profile.');
    };

    document.addEventListener('input', function (e) {
        if (e.target.matches('.salary-input, .tds-input, .gross-input, .pt-input, .pf-input')) calculateSalary();
    });
    var dirty = false;
    document.getElementById('salaryForm').addEventListener('input', function () { dirty = true; });
    document.getElementById('salaryForm').addEventListener('submit', function () { dirty = false; });
    window.addEventListener('beforeunload', function (e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
    document.addEventListener('DOMContentLoaded', calculateSalary);
})();
</script>
@endsection

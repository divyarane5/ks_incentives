@extends('layouts.app')

@section('content')
@include('hr.partials.hr-styles')
@php $inr = fn ($n) => \App\Support\PeopleFormat::inr($n, 0, false); @endphp
<div class="container-xxl flex-grow-1 container-p-y kp" style="max-width:1500px">
    <div class="kp-crumb"><a href="{{ route('users.index') }}">KeyPeople</a> <i class="bx bx-chevron-right"></i> <span>Salary register</span></div>
    <div class="hr-head">
        <div><h4>Salary register · {{ $date->format('F Y') }}</h4><div class="sub">Everyone with salary saved for this month. Amounts come from each person's monthly salary page.</div></div>
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <select name="month" class="form-select raw-select" style="width:auto">
                @foreach([4,5,6,7,8,9,10,11,12,1,2,3] as $m)
                    <option value="{{ $m }}" {{ $m === $month ? 'selected' : '' }}>{{ \Carbon\Carbon::create(2000, $m, 1)->format('F') }}</option>
                @endforeach
            </select>
            <select name="fy" class="form-select raw-select" style="width:auto">
                @foreach($fyOptions as $opt)<option value="{{ $opt }}" {{ $opt === $fy ? 'selected' : '' }}>FY {{ $opt }}</option>@endforeach
            </select>
            <button class="btn btn-primary">Show</button>
            @if($rows->count())
                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv', 'fy' => $fy, 'month' => $month]) }}" class="btn btn-outline-success"><i class="bx bx-download"></i> Excel</a>
            @endif
        </form>
    </div>

    <div class="hr-bal">
        <div class="kp-fact"><div class="l">People paid</div><div class="v">{{ $rows->count() }}</div><div class="s">{{ $missingBank ? $missingBank.' missing bank details' : 'all have bank details' }}</div></div>
        <div class="kp-fact"><div class="l">Net to bank</div><div class="v">₹ {{ $inr($totals['net']) }}</div><div class="s">salary credited</div></div>
        <div class="kp-fact"><div class="l">PF + PT + TDS</div><div class="v">₹ {{ $inr($totals['pf'] + $totals['pt'] + $totals['tds']) }}</div><div class="s">PF {{ $inr($totals['pf']) }} · PT {{ $inr($totals['pt']) }} · TDS {{ $inr($totals['tds']) }}</div></div>
        <div class="kp-fact"><div class="l">Total cost</div><div class="v">₹ {{ $inr($totals['cost']) }}</div><div class="s">loss of pay {{ $inr($totals['lop']) }}</div></div>
    </div>

    <div class="kp-panel">
        @if($rows->count())
            <div class="table-responsive">
                <table class="hr-table">
                    <thead><tr>
                        <th>Employee</th><th>Bank</th>
                        <th class="num">Gross</th><th class="num">Loss of pay</th><th class="num">PF</th><th class="num">PT</th><th class="num">TDS</th><th class="num">Net credited</th><th></th>
                    </tr></thead>
                    <tbody>
                        @foreach($rows as $r)
                            @php $u = $r->user; @endphp
                            <tr>
                                <td style="min-width:180px">
                                    <a href="{{ route('users.salary.index', [$u->id, 'fy' => $fy]) }}" class="fw-semibold">{{ $u->name }}</a>
                                    <div class="small text-muted">{{ $u->employee_code }} · {{ $u->designation?->name }}</div>
                                </td>
                                <td style="min-width:150px">
                                    @if($u->bank_account_number && $u->ifsc_code)
                                        <div class="small">{{ $u->bank_name }}</div>
                                        <div class="small text-muted">{{ $u->bank_account_number }} · {{ $u->ifsc_code }}</div>
                                    @else
                                        <span class="hr-status pending">Bank details missing</span>
                                    @endif
                                </td>
                                <td class="num">{{ $inr($r->gross_salary) }}</td>
                                <td class="num">{{ $r->extra_deduction > 0 ? $inr($r->extra_deduction) : '' }}</td>
                                <td class="num">{{ $inr($r->pf_amount) }}</td>
                                <td class="num">{{ $inr($r->professional_tax) }}</td>
                                <td class="num">{{ $r->tds > 0 ? $inr($r->tds) : '' }}</td>
                                <td class="num fw-bold">{{ $inr($r->salary_credited) }}</td>
                                <td class="text-end"><a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('payslip.pdf', [$u->id, $fy, $month]) }}">Payslip</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr>
                        <td>Total ({{ $rows->count() }})</td><td></td>
                        <td class="num">{{ $inr($totals['gross']) }}</td><td class="num">{{ $inr($totals['lop']) }}</td><td class="num">{{ $inr($totals['pf']) }}</td>
                        <td class="num">{{ $inr($totals['pt']) }}</td><td class="num">{{ $inr($totals['tds']) }}</td><td class="num">{{ $inr($totals['net']) }}</td><td></td>
                    </tr></tfoot>
                </table>
            </div>
        @else
            <div class="kp-panel-b"><div class="kp-empty">No salary saved for {{ $date->format('F Y') }} yet. Enter it on each person's salary page.</div></div>
        @endif
    </div>
</div>
@endsection

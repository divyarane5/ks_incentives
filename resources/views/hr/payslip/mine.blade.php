@extends('layouts.app')

@section('content')
@include('hr.partials.hr-styles')
@php $inr = fn ($n) => \App\Support\PeopleFormat::inr($n); @endphp
<div class="container-xxl flex-grow-1 container-p-y kp">
    <div class="hr-head"><div><h4>My payslips</h4><div class="sub">Tap a month to open the payslip PDF.</div></div></div>

    <div class="kp-panel">
        <div class="kp-panel-b">
            @forelse($rows->groupBy('financial_year') as $fy => $list)
                <div class="fw-bold pt-3 pb-1" style="color:var(--kp-navy)">FY {{ $fy }}</div>
                <ul class="kp-list">
                    @foreach($list as $r)
                        <li>
                            <div class="ic"><i class="bx bx-receipt"></i></div>
                            <div class="bd">
                                <div class="t">{{ $r->date->format('F Y') }}</div>
                                <div class="m">Net {{ $inr($r->salary_credited) }}{{ $r->extra_deduction > 0 ? ' · loss of pay '.$inr($r->extra_deduction) : '' }}</div>
                            </div>
                            <div class="r d-flex gap-1">
                                <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('payslip.pdf', [$user->id, $r->financial_year, $r->month]) }}">Open</a>
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('payslip.pdf', [$user->id, $r->financial_year, $r->month]) }}?download=1" title="Download"><i class="bx bx-download"></i></a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @empty
                <div class="kp-empty">No payslips yet. They appear here once HR saves your monthly salary.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

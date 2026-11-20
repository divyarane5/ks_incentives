@extends('layouts.app')

@section('content')
@php
    use App\Services\QuarterlyIncentiveService as Q;
    $inr = fn ($v) => number_format((float) $v, 0);
@endphp
<div class="container-xxl container-p-y">
    <h4 class="fw-bold mb-1"><span class="text-muted fw-light">KeyEarn /</span> My incentive</h4>
    <p class="text-muted mb-4">Worked out each quarter on the year so far: your collection compared with your salary. What earlier quarters already paid (froze) is taken off.</p>

    <form method="GET" class="mb-4" style="max-width: 220px">
        <label class="form-label">Financial year</label>
        <select name="fy" class="form-select" onchange="this.form.submit()">
            @foreach ($years as $y)<option value="{{ $y }}" @selected($y === $fy)>{{ $y }}</option>@endforeach
        </select>
    </form>

    @foreach ($quarters as $q => $info)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ Q::quarterLabel($q) }} {{ $fy }}</h5>
                @if ($info['frozen'])<span class="badge bg-success">Final</span>@else<span class="badge bg-label-warning">Estimate, can still change</span>@endif
            </div>
            <div class="card-body">
                @forelse ($info['rows'] as $r)
                    <div class="row g-3 mb-2">
                        <div class="col-6 col-md-2"><div class="text-muted small">As</div><div class="fw-semibold">{{ $r['role'] }}</div></div>
                        <div class="col-6 col-md-2"><div class="text-muted small">Salary so far</div><div>₹{{ $inr($r['ytd_salary']) }}</div></div>
                        <div class="col-6 col-md-2"><div class="text-muted small">Collection so far</div><div>₹{{ $inr($r['ytd_collection']) }}</div></div>
                        <div class="col-6 col-md-2"><div class="text-muted small">Times</div><div>{{ number_format((float) $r['performance_times'], 2) }}x · {{ rtrim(rtrim(number_format((float) $r['slab_percent'], 2), '0'), '.') }}%</div></div>
                        <div class="col-6 col-md-2"><div class="text-muted small">Incentive so far</div><div>₹{{ $inr($r['ytd_incentive']) }}</div></div>
                        <div class="col-6 col-md-2"><div class="text-muted small">This quarter</div><div class="fs-5 fw-semibold">₹{{ $inr($r['payable']) }}</div></div>
                    </div>
                    @if (! empty($r['note']))<div class="small text-muted">{{ $r['note'] }}</div>@endif
                @empty
                    <div class="text-muted">No collections counted for you up to {{ \Carbon\Carbon::parse($info['period'][1])->format('d M Y') }}.</div>
                @endforelse
            </div>
        </div>
    @endforeach
</div>
@endsection

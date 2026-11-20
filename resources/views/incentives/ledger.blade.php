@extends('layouts.app')

@section('content')
@php
    use App\Services\QuarterlyIncentiveService as Q;
    $inr = fn ($v) => number_format((float) $v, 0);
    $totals = collect($rows)->groupBy('role')->map(fn ($g) => ['people' => $g->count(), 'payable' => $g->sum('payable')]);
@endphp
<style>
    .ledger-table th { white-space: nowrap; font-size: .75rem; text-transform: none; }
    .ledger-table td { white-space: nowrap; font-size: .85rem; }
    .ledger-table td.num, .ledger-table th.num { text-align: right; font-variant-numeric: tabular-nums; }
    .row-left td { color: #8592a3; }
</style>
<div class="container-xxl container-p-y">

    <h4 class="fw-bold mb-1">Incentives: Ledger &amp; Freeze</h4>
    <p class="text-muted mb-4">
        Each quarter is worked out on the year so far ({{ \Carbon\Carbon::parse($period[0])->format('d M Y') }}
        to {{ \Carbon\Carbon::parse($period[1])->format('d M Y') }}).
        Payable = incentive so far minus what earlier quarters froze. People who have left are not paid.
    </p>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('incentive-ledger.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Financial year</label>
                    <select name="fy" class="form-select" onchange="this.form.submit()">
                        @foreach ($years as $y)
                            <option value="{{ $y }}" @selected($y === $fy)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Quarter</label>
                    <select name="quarter" class="form-select" onchange="this.form.submit()">
                        @foreach ([1, 2, 3, 4] as $q)
                            <option value="{{ $q }}" @selected($q === $quarter)>
                                {{ Q::quarterLabel($q) }}{{ $quarterStatus[$q] ? ' - frozen' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" onchange="this.form.submit()">
                        <option value="">All</option>
                        @foreach (Q::ROLES as $r)
                            <option value="{{ $r }}" @selected($r === $role)>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 text-md-end">
                    <a class="btn btn-outline-secondary" href="{{ route('incentive-ledger.export', ['fy' => $fy, 'quarter' => $quarter]) }}">Download CSV</a>
                </div>
            </form>
        </div>
    </div>

    @foreach ($warnings as $w)
        <div class="alert alert-warning py-2">{{ $w }}</div>
    @endforeach

    <div class="card mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                @if ($frozen)
                    <span class="badge bg-success">Frozen</span>
                    <span class="ms-2">
                        {{ Q::quarterLabel($quarter) }} {{ $fy }} was frozen
                        {{ optional($frozenInfo?->frozen_at)->format('d M Y, h:i A') }}
                        by {{ $frozenInfo?->frozenBy?->name ?? 'unknown' }}. These numbers no longer change.
                    </span>
                @else
                    <span class="badge bg-label-warning">Not frozen</span>
                    <span class="ms-2">These numbers are live and change as payments and salaries are entered.</span>
                @endif
            </div>
            <div class="d-flex gap-2">
                @if (! $frozen && $canFreeze)
                    <form method="POST" action="{{ route('incentive-ledger.freeze') }}"
                          onsubmit="return confirm('Freeze {{ Q::quarterLabel($quarter) }} {{ $fy }}? The numbers will be locked.');">
                        @csrf
                        <input type="hidden" name="fy" value="{{ $fy }}">
                        <input type="hidden" name="quarter" value="{{ $quarter }}">
                        <button class="btn btn-primary">Freeze this quarter</button>
                    </form>
                @endif
                @if ($frozen && $canUnfreeze)
                    <form method="POST" action="{{ route('incentive-ledger.unfreeze') }}"
                          onsubmit="return confirm('Unfreeze {{ Q::quarterLabel($quarter) }} {{ $fy }}? It will be worked out again from current data.');">
                        @csrf
                        <input type="hidden" name="fy" value="{{ $fy }}">
                        <input type="hidden" name="quarter" value="{{ $quarter }}">
                        <button class="btn btn-outline-danger">Unfreeze</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="row mb-4 g-3">
        @foreach (Q::ROLES as $r)
            <div class="col-6 col-md-3">
                <div class="card h-100">
                    <div class="card-body py-3">
                        <div class="text-muted small">{{ $r }} payable</div>
                        <div class="fs-5 fw-semibold">₹{{ $inr($totals[$r]['payable'] ?? 0) }}</div>
                        <div class="text-muted small">{{ $totals[$r]['people'] ?? 0 }} people</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm table-hover ledger-table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th class="num" title="FOS: own salary. Managers: salary of the whole team including the manager.">Salary so far</th>
                        <th class="num" title="Brokerage received before GST (bank amount + TDS)">Collection so far</th>
                        <th class="num">Times</th>
                        <th class="num">Justification</th>
                        <th class="num" title="FOS: collection above justification. Managers: incentive earned by their FOS on the team's bookings.">Incentive base</th>
                        <th class="num">Slab %</th>
                        <th class="num">Incentive so far</th>
                        <th class="num">Frozen earlier</th>
                        <th class="num">Payable now</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $r)
                        <tr class="{{ $r['status'] === 'Exited' ? 'row-left' : '' }}">
                            <td>
                                {{ $r['name'] }}
                                <div class="text-muted small">{{ $r['employee_code'] }}</div>
                            </td>
                            <td>{{ $r['role'] }}</td>
                            <td class="num">{{ $inr($r['ytd_salary']) }}</td>
                            <td class="num">{{ $inr($r['ytd_collection']) }}</td>
                            <td class="num">{{ number_format((float) $r['performance_times'], 2) }}x</td>
                            <td class="num">{{ $inr($r['justification_amount']) }}</td>
                            <td class="num">{{ $inr($r['incentive_base']) }}</td>
                            <td class="num">{{ rtrim(rtrim(number_format((float) $r['slab_percent'], 2), '0'), '.') }}%</td>
                            <td class="num">{{ $inr($r['ytd_incentive']) }}</td>
                            <td class="num">{{ $inr($r['previously_frozen']) }}</td>
                            <td class="num fw-semibold">{{ $inr($r['payable']) }}</td>
                            <td class="small">{{ $r['note'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="12" class="text-center text-muted py-4">No collections in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

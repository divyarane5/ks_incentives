@extends('layouts.app')

@section('content')
@include('hr.partials.hr-styles')
@php $d = fn ($n) => rtrim(rtrim(number_format((float) $n, 1), '0'), '.'); @endphp
<div class="container-xxl flex-grow-1 container-p-y kp" style="max-width:1300px">
    <div class="kp-crumb"><a href="{{ route('users.index') }}">KeyPeople</a> <i class="bx bx-chevron-right"></i> <span>Leave</span></div>
    <div class="hr-head">
        <div><h4>Leave</h4><div class="sub">FY {{ $fy }} · everyone's requests and balances</div></div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('leave.setup') }}" class="btn btn-outline-primary"><i class="bx bx-cog"></i> Leave types &amp; holidays</a>
            <form method="GET">
                <input type="hidden" name="status" value="{{ $status }}">
                @if($person)<input type="hidden" name="user_id" value="{{ $person->id }}">@endif
                <select name="fy" class="form-select raw-select" onchange="this.form.submit()">
                    @foreach([\App\Services\LeaveService::fy(now()->addYear()), \App\Services\LeaveService::fy(), \App\Services\LeaveService::fy(now()->subYear()), \App\Services\LeaveService::fy(now()->subYears(2))] as $opt)
                        <option value="{{ $opt }}" {{ $opt === $fy ? 'selected' : '' }}>FY {{ $opt }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="hr-pills">
                @foreach(['pending' => 'Waiting', 'approved' => 'Approved', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled', '' => 'All'] as $k => $label)
                    <a href="{{ request()->fullUrlWithQuery(['status' => $k, 'page' => null]) }}" class="{{ $status === $k ? 'on' : '' }}">
                        {{ $label }}@if($k !== '') ({{ $counts[$k] ?? 0 }})@endif
                    </a>
                @endforeach
            </div>
            <div class="kp-panel">
                <div class="kp-panel-h">
                    <h2><i class="bx bx-list-ul"></i>{{ $person ? $person->name : 'Requests' }}</h2>
                    @if($person)<a href="{{ request()->fullUrlWithQuery(['user_id' => null]) }}" class="small">Show everyone</a>@endif
                </div>
                <div class="kp-panel-b">
                    @if($leaves->count())
                        <ul class="kp-list">
                            @foreach($leaves as $r) @include('hr.leave._request', ['r' => $r, 'decide' => true, 'cancel' => true, 'showApprover' => true]) @endforeach
                        </ul>
                        <div class="mt-2">{{ $leaves->links('pagination::bootstrap-5') }}</div>
                    @else
                        <div class="kp-empty">Nothing here.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-plane-take-off"></i>On leave today</h2><span class="kp-note">{{ $todayAway->count() }}</span></div>
                <div class="kp-panel-b">
                    @forelse($todayAway as $r)
                        <ul class="kp-list"><li><div class="bd"><div class="t">{{ $r->user?->name }}</div><div class="m">{{ $r->type?->name }} · {{ $r->dates_label }}</div></div></li></ul>
                    @empty
                        <div class="kp-empty">Everyone is in today.</div>
                    @endforelse
                </div>
            </div>

            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-user-check"></i>One person's balance</h2></div>
                <div class="kp-panel-b" style="padding:14px">
                    <form method="GET" class="hr-form mb-2">
                        <input type="hidden" name="fy" value="{{ $fy }}">
                        <input type="hidden" name="status" value="">
                        <select name="user_id" class="form-select" onchange="this.form.submit()" data-live-search="true">
                            <option value="">Choose a person…</option>
                            @foreach($people as $p)
                                <option value="{{ $p->id }}" {{ $person?->id === $p->id ? 'selected' : '' }}>{{ $p->name }}{{ $p->employee_code ? ' ('.$p->employee_code.')' : '' }}</option>
                            @endforeach
                        </select>
                    </form>

                    @if($person)
                        <table class="hr-table mb-3">
                            <thead><tr><th>Type</th><th class="num">Quota</th><th class="num">+/-</th><th class="num">Used</th><th class="num">Left</th></tr></thead>
                            <tbody>
                                @foreach($balances as $b)
                                    <tr>
                                        <td><span class="hr-dot" style="background:{{ $b['type']->color }}"></span>{{ $b['type']->name }}</td>
                                        <td class="num">{{ $b['type']->is_paid ? $d($b['quota']) : '—' }}</td>
                                        <td class="num">{{ $b['adjusted'] ? ($b['adjusted'] > 0 ? '+' : '').$d($b['adjusted']) : '' }}</td>
                                        <td class="num">{{ $d($b['used']) }}{{ $b['pending'] ? ' (+'.$d($b['pending']).')' : '' }}</td>
                                        <td class="num fw-bold">{{ $b['type']->is_paid ? $d($b['available']) : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="fw-bold mb-2" style="color:var(--kp-navy)">Add or take away days</div>
                        <form method="POST" action="{{ route('leave.adjust') }}" class="hr-form row g-2 mb-3">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $person->id }}">
                            <input type="hidden" name="financial_year" value="{{ $fy }}">
                            <div class="col-7">
                                <select name="leave_type_id" class="form-select raw-select">
                                    @foreach($types->where('is_paid', true) as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-5"><input type="number" step="0.5" name="days" class="form-control" placeholder="+2 or -1" required></div>
                            <div class="col-12"><input type="text" name="note" class="form-control" placeholder="Why, e.g. carried forward from last year" required maxlength="255"></div>
                            <div class="col-12"><button class="btn btn-outline-primary w-100">Save change</button></div>
                        </form>
                        @if($adjustments->count())
                            <ul class="kp-list mb-3">
                                @foreach($adjustments as $a)
                                    <li><div class="bd"><div class="t">{{ $a->days > 0 ? '+' : '' }}{{ $d($a->days) }} {{ $a->type?->name }}</div><div class="m">{{ $a->note }} · {{ $a->creator?->name }}, {{ $a->created_at->format('d M') }}</div></div></li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="fw-bold mb-2" style="color:var(--kp-navy)">Add leave for {{ $person->first_name ?: $person->name }}</div>
                        <form method="POST" action="{{ route('leave.store') }}" class="hr-form row g-2">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $person->id }}">
                            <div class="col-12">
                                <select name="leave_type_id" class="form-select raw-select">
                                    @foreach($types as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-6"><input type="date" name="from_date" class="form-control" required></div>
                            <div class="col-6"><input type="date" name="to_date" class="form-control" required></div>
                            <div class="col-12"><input type="text" name="reason" class="form-control" placeholder="Reason (optional)"></div>
                            <div class="col-12"><button class="btn btn-primary w-100">Add as approved</button></div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
@include('hr.partials.hr-styles')
@php $d = fn ($n) => rtrim(rtrim(number_format((float) $n, 1), '0'), '.'); @endphp

<div class="container-xxl flex-grow-1 container-p-y kp">
    <div class="hr-head">
        <div>
            <h4>My leave</h4>
            <div class="sub">Leave year FY {{ $fy }} (April to March){{ $approver ? ' · Approved by '.$approver->name : ' · Approved by HR' }}</div>
        </div>
        <div class="d-flex gap-2">
            @if($teamPending)
                <a href="{{ route('leave.team') }}" class="btn btn-kp"><i class="bx bx-check-double"></i> Approve team leave <span class="badge bg-danger ms-1">{{ $teamPending }}</span></a>
            @endif
            <form method="GET">
                <select name="fy" class="form-select raw-select" onchange="this.form.submit()">
                    @foreach([\App\Services\LeaveService::fy(now()->addYear()), \App\Services\LeaveService::fy(), \App\Services\LeaveService::fy(now()->subYear())] as $opt)
                        <option value="{{ $opt }}" {{ $opt === $fy ? 'selected' : '' }}>FY {{ $opt }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="hr-bal">
        @foreach($balances as $b)
            <div class="kp-fact" style="--c: {{ $b['type']->color ?: 'var(--kp-gold)' }}">
                <div class="l">{{ $b['type']->name }}</div>
                @if($b['type']->is_paid)
                    <div class="v">{{ $d($b['available']) }} <small>left</small></div>
                    <div class="s">of {{ $d($b['quota'] + $b['adjusted']) }} · used {{ $d($b['used']) }}@if($b['pending']) · {{ $d($b['pending']) }} waiting @endif</div>
                @else
                    <div class="v">{{ $d($b['used']) }} <small>taken</small></div>
                    <div class="s">no pay for these days</div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-5">
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-calendar-plus"></i>Apply for leave</h2></div>
                <div class="kp-panel-b" style="padding:14px">
                    @if($errors->any())
                        <div class="alert alert-danger py-2">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                    @endif
                    <form method="POST" action="{{ route('leave.store') }}" class="hr-form row g-3" id="applyForm">
                        @csrf
                        <div class="col-12">
                            <label class="form-label">Type of leave</label>
                            <select name="leave_type_id" class="form-select raw-select" required>
                                @foreach($types as $t)
                                    @php $b = collect($balances)->firstWhere('type.id', $t->id); @endphp
                                    <option value="{{ $t->id }}" {{ old('leave_type_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}{{ $t->is_paid ? ' ('.$d($b['available'] ?? 0).' left)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">From</label>
                            <input type="date" name="from_date" id="fromDate" class="form-control" value="{{ old('from_date', today()->toDateString()) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">To</label>
                            <input type="date" name="to_date" id="toDate" class="form-control" value="{{ old('to_date', today()->toDateString()) }}" required>
                        </div>
                        <div class="col-12" id="halfWrap">
                            <div class="hr-seg">
                                <label><input type="radio" name="half_day" value="" {{ old('half_day') ? '' : 'checked' }}><span>Full day</span></label>
                                <label><input type="radio" name="half_day" value="first" {{ old('half_day') === 'first' ? 'checked' : '' }}><span>First half</span></label>
                                <label><input type="radio" name="half_day" value="second" {{ old('half_day') === 'second' ? 'checked' : '' }}><span>Second half</span></label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Reason</label>
                            <textarea name="reason" class="form-control" rows="2" maxlength="1000" placeholder="e.g. Family function">{{ old('reason') }}</textarea>
                        </div>
                        <div class="col-12 d-flex align-items-center justify-content-between gap-2">
                            <span class="hr-count" id="dayCount">&nbsp;</span>
                            <button type="submit" class="btn btn-primary" style="min-height:46px;border-radius:10px;font-weight:700;min-width:150px"><i class="bx bx-send"></i> Send request</button>
                        </div>
                        <div class="col-12 form-text mt-0">Weekly off{{ $offs ? '' : 's' }} and holidays aren't counted.</div>
                    </form>
                </div>
            </div>

            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-party"></i>Coming holidays</h2></div>
                <div class="kp-panel-b">
                    @forelse($holidays as $h)
                        <ul class="kp-list"><li>
                            <div class="ic" style="flex-direction:column;line-height:1;font-size:.7rem;font-weight:700"><span style="font-size:1rem">{{ $h->date->format('d') }}</span>{{ $h->date->format('M') }}</div>
                            <div class="bd"><div class="t">{{ $h->name }}</div><div class="m">{{ $h->date->format('l') }}</div></div>
                        </li></ul>
                    @empty
                        <div class="kp-empty">No holidays added yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-list-ul"></i>My requests</h2><span class="kp-note">FY {{ $fy }}</span></div>
                <div class="kp-panel-b">
                    @forelse($requests as $r)
                        <ul class="kp-list"><li style="align-items:flex-start">
                            <div class="ic" style="color:{{ $r->type?->color }}"><i class="bx bx-calendar"></i></div>
                            <div class="bd">
                                <div class="t">{{ $r->dates_label }}</div>
                                <div class="m"><span class="hr-dot" style="background:{{ $r->type?->color ?? '#999' }}"></span>{{ $r->type?->name }} · {{ $d($r->days) }} {{ $r->days == 1 ? 'day' : 'days' }}</div>
                                @if($r->reason)<div class="m">“{{ $r->reason }}”</div>@endif
                                @if($r->decision_note || $r->decider)
                                    <div class="m">{{ $r->decider ? ucfirst($r->status).' by '.$r->decider->name : '' }}{{ $r->decision_note ? ': '.$r->decision_note : '' }}</div>
                                @elseif($r->status === 'pending')
                                    <div class="m">Waiting for {{ $r->approver?->name ?? 'HR' }}</div>
                                @endif
                            </div>
                            <div class="r">
                                <span class="hr-status {{ $r->status }}">{{ \App\Models\LeaveRequest::STATUSES[$r->status] ?? $r->status }}</span>
                                @if($r->status === 'pending' || ($r->status === 'approved' && $r->from_date->gt(today())))
                                    <form method="POST" action="{{ route('leave.cancel', $r) }}" class="mt-2" onsubmit="return confirm('Cancel this leave?')">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-secondary">Cancel</button>
                                    </form>
                                @endif
                            </div>
                        </li></ul>
                    @empty
                        <div class="kp-empty">No leave this year yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
(function () {
    var from = document.getElementById('fromDate'), to = document.getElementById('toDate');
    var half = document.getElementById('halfWrap'), out = document.getElementById('dayCount');
    var timer;
    function refresh() {
        if (to.value < from.value) to.value = from.value;
        var single = from.value === to.value;
        half.style.display = single ? '' : 'none';
        if (!single) document.querySelector('input[name=half_day][value=""]').checked = true;
        var h = (document.querySelector('input[name=half_day]:checked') || {}).value || '';
        clearTimeout(timer);
        timer = setTimeout(function () {
            fetch(@json(route('leave.count')) + '?from=' + from.value + '&to=' + to.value + '&half=' + h, { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (j) { out.textContent = j.days > 0 ? j.days + (j.days == 1 ? ' day' : ' days') + ' of leave' : 'No working days in these dates'; })
                .catch(function () { out.textContent = ''; });
        }, 200);
    }
    from.addEventListener('change', refresh);
    to.addEventListener('change', refresh);
    document.querySelectorAll('input[name=half_day]').forEach(function (r) { r.addEventListener('change', refresh); });
    refresh();
})();
</script>
@endsection

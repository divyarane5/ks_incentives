@extends('layouts.app')

{{-- RealtySuite home: one section per module this person can open. --}}
@php
    $money = function ($v) {
        $v = (float) $v;
        if ($v >= 10000000) return '₹' . rtrim(rtrim(number_format($v / 10000000, 2), '0'), '.') . ' Cr';
        if ($v >= 100000) return '₹' . rtrim(rtrim(number_format($v / 100000, 2), '0'), '.') . ' L';
        return '₹' . number_format($v);
    };
    $u = auth()->user();
    $tile = fn ($label, $value, $href = null, $tone = '') => compact('label', 'value', 'href', 'tone');
@endphp

@section('content')
<style>
    .rs-sec { margin-bottom: 1.75rem; }
    .rs-sec-head { display: flex; align-items: center; gap: .6rem; margin-bottom: .75rem; }
    .rs-sec-head .bx { font-size: 1.5rem; color: var(--bs-primary, #696cff); }
    .rs-sec-head h5 { margin: 0; font-weight: 700; }
    .rs-sec-head .rs-sub { color: #8592a3; font-size: .8rem; }
    .rs-sec-head .rs-open { margin-left: auto; font-size: .85rem; white-space: nowrap; }
    .rs-tile { display: block; height: 100%; }
    .rs-tile .card-body { padding: .9rem 1rem; }
    .rs-tile .rs-label { color: #8592a3; font-size: .8rem; line-height: 1.2; }
    .rs-tile .rs-value { font-size: 1.4rem; font-weight: 700; margin-top: .25rem; color: #384551; }
    .rs-tile .rs-value.warn { color: #e6a100; }
    .rs-tile .rs-value.bad { color: #ff3e1d; }
    .rs-tile .rs-value.good { color: #39b54a; }
    .rs-group { font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; color: #a1acb8; margin: .25rem 0 .5rem; font-weight: 600; }
</style>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
        <h4 class="fw-bold mb-0 me-auto">RealtySuite</h4>
        @if (isset($s['keybookings']) || isset($s['keyaccounts']) || isset($s['keyearn']))
            <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center gap-2">
                <label for="fy" class="small text-muted mb-0">Financial year</label>
                <select id="fy" name="fy" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                    @foreach ($fyOptions as $y => $label)
                        <option value="{{ $y }}" @selected($y == $fyStart)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    @include('hr.celebrations._dashboard')

    @if (! count($mods))
        <div class="card"><div class="card-body text-muted">
            You don't have any modules yet. Ask your admin to give you access.
        </div></div>
    @endif

    @foreach ($mods as $key)
        @php
            $m = \App\Support\Modules::ALL[$key];
            $d = $s[$key] ?? null;
            $open = null; $sub = $m['about']; $groups = [];

            if ($key === 'keyleads' && $d) {
                $open = route('crm.dashboard');
                $sub = $d['scope'];
                $groups[''] = [
                    $tile('New today', number_format($d['new_today']), route('crm.leads.index', ['tab' => $u->isAdmin() ? 'all' : 'mine', 'from' => now()->toDateString()])),
                    $tile('Not picked up yet', number_format($d['not_picked']), route('crm.dashboard'), $d['not_picked'] ? 'warn' : ''),
                    $tile('Follow-ups today', number_format($d['due_today']), route('crm.leads.index', ['tab' => 'today'])),
                    $tile('Overdue follow-ups', number_format($d['overdue']), route('crm.leads.index', ['tab' => 'overdue']), $d['overdue'] ? 'bad' : ''),
                    $tile('Open leads', number_format($d['open']), route('crm.leads.index', ['tab' => $u->isAdmin() ? 'all' : 'mine'])),
                ];
            }

            if ($key === 'keybookings' && $d) {
                if (isset($d['bookings'])) {
                    $b = $d['bookings'];
                    $open = route('booking.index');
                    $groups[$b['scope'] . ' · FY ' . $fyLabel] = [
                        $tile('Approved bookings', number_format($b['approved']), route('booking.index')),
                        $tile('Booked this month', number_format($b['this_month']), route('booking.index')),
                        $tile('Waiting for approval', number_format($b['waiting']), route('booking.index'), $b['waiting'] ? 'warn' : ''),
                        $tile('Cancelled', number_format($b['cancelled']), route('booking.index')),
                        $tile('Agreement value', $money($b['agreement']), $b['scope'] === 'All bookings' ? url('/incentive-dashboard-view') : route('booking.index')),
                    ];
                }
                if (isset($d['mandate'])) {
                    $md = $d['mandate'];
                    $open = $open ?? route('mandate_projects.index');
                    $groups['Mandate'] = array_values(array_filter([
                        $tile('Mandate projects', number_format($md['projects']), $u->can('mandate_project-view') ? route('mandate_projects.index') : null),
                        $tile('Mandate bookings', number_format($md['bookings']), $u->can('mandate-booking-view') ? route('mandate_bookings.index') : null),
                        $tile('Pending mandate bookings', number_format($md['pending']), $u->can('mandate-booking-view') ? route('mandate_bookings.index') : null, $md['pending'] ? 'warn' : ''),
                        $u->can('channel-partner-view') ? $tile('Channel partners', number_format($md['partners']), route('channel_partners.index')) : null,
                        $u->can('client-enquiry-view') ? $tile('Client enquiries', number_format($md['enquiries']), route('client-enquiries.index')) : null,
                    ]));
                }
            }

            if ($key === 'keyaccounts' && $d) {
                $open = route('brokerage-payments.index');
                $groups['Brokerage · FY ' . $fyLabel . ' bookings'] = [
                    $tile('Brokerage earned', $money($d['brokerage']), route('brokerage-payments.index')),
                    $tile('Invoiced', $money($d['invoiced']), route('brokerage-payments.index')),
                    $tile('Received', $money($d['received']), route('brokerage-payments.index'), 'good'),
                ];
                $groups['To act on · all years'] = [
                    $tile('Invoiced, not yet received', $money($d['to_collect']), route('brokerage-payments.aging'), $d['to_collect'] ? 'bad' : ''),
                    $tile('Bookings not invoiced', number_format($d['not_invoiced']), route('brokerage-payments.index'), $d['not_invoiced'] ? 'warn' : ''),
                    $tile('Payment follow-ups due', number_format($d['followups_due']), route('brokerage-payments.index'), $d['followups_due'] ? 'warn' : ''),
                ];
            }

            if ($key === 'keypeople' && $d) {
                $open = route('users.index');
                $groups[''] = [
                    $tile('Active employees', number_format($d['active']), route('users.index')),
                    $tile('Joined this month', number_format($d['joined']), route('users.index'), 'good'),
                    $tile('Left this month', number_format($d['left']), route('users.index'), $d['left'] ? 'bad' : ''),
                    $tile('Not yet confirmed (joined in last 6 months)', number_format($d['on_probation']), route('users.index')),
                ];
            }

            if ($key === 'keyearn' && $d) {
                $open = route('my-incentive');
                $groups['Your incentive · FY ' . $fyLabel] = [
                    $tile('Earned so far this year', $d['my_quarters'] ? $money($d['my_ytd']) : 'Not out yet', route('my-incentive')),
                    $tile('Paid to you', $d['my_quarters'] ? $money($d['my_paid']) : '—', route('my-incentive'), 'good'),
                ];
                if (isset($d['all_paid'])) {
                    $groups['Everyone · FY ' . $fyLabel] = [
                        $tile('Incentive paid', $money($d['all_paid']), route('incentive-ledger.index'), 'good'),
                        $tile('People paid', number_format($d['all_people']), route('incentive-ledger.index')),
                        $tile('Last quarter frozen', $d['last_quarter'] ? 'Q' . $d['last_quarter'] : 'None yet', route('incentive-ledger.index')),
                    ];
                }
            }
        @endphp

        <div class="rs-sec">
            <div class="rs-sec-head">
                <i class="bx {{ $m['icon'] }}"></i>
                <div>
                    <h5>{{ $m['name'] }}</h5>
                    <div class="rs-sub">{{ $sub }}</div>
                </div>
                @if ($open)
                    <a class="rs-open" href="{{ $open }}">Open <i class="bx bx-right-arrow-alt align-middle"></i></a>
                @endif
            </div>

            @forelse ($groups as $title => $tiles)
                @if ($title !== '')
                    <div class="rs-group">{{ $title }}</div>
                @endif
                <div class="row g-3 mb-3">
                    @foreach ($tiles as $t)
                        <div class="col-6 col-md-4 col-xl">
                            @if ($t['href'])
                                <a href="{{ $t['href'] }}" class="card rs-tile text-body">
                            @else
                                <div class="card rs-tile">
                            @endif
                                <div class="card-body">
                                    <div class="rs-label">{{ $t['label'] }}</div>
                                    <div class="rs-value {{ $t['tone'] }}">{{ $t['value'] }}</div>
                                </div>
                            @if ($t['href'])</a>@else</div>@endif
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="card"><div class="card-body text-muted small">
                    You can open {{ $m['name'] }}, but none of its pages are switched on for you yet.
                </div></div>
            @endforelse
        </div>
    @endforeach
</div>
@endsection

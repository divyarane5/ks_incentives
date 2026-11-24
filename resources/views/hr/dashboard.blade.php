@extends('layouts.app')

@php
    use App\Support\PeopleFormat as F;
    use App\Services\CelebrationService as CS;
    $avatar = function ($u, $size = 38) {
        $photo = $u && $u->photo && is_file(public_path('storage/'.$u->photo)) ? asset('storage/'.$u->photo) : null;
        return '<span class="hd-av" style="width:'.$size.'px;height:'.$size.'px;'.($photo ? "background-image:url('".e($photo)."')" : '').'">'.($photo ? '' : e(F::initials($u?->name))).'</span>';
    };
    $bars = function ($rows, $limit = 6) {
        $max = max(1, (int) $rows->max());
        $out = '';
        foreach ($rows->take($limit) as $label => $n) {
            $out .= '<div class="hd-bar"><div class="l"><span>'.e($label).'</span><b>'.$n.'</b></div><div class="t"><i style="width:'.round($n * 100 / $max).'%"></i></div></div>';
        }
        if ($rows->count() > $limit) {
            $out .= '<div class="hd-more">+ '.($rows->count() - $limit).' more</div>';
        }
        return $out;
    };
    $can = fn ($route) => \Illuminate\Support\Facades\Route::has($route);
@endphp

@section('content')
@include('hr.partials.hr-styles')
<style>
    .hd-actions { display: flex; gap: 8px; overflow-x: auto; scrollbar-width: none; padding-bottom: 2px; margin-bottom: 14px; }
    .hd-actions::-webkit-scrollbar { display: none; }
    .hd-actions a { white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; padding: 9px 13px; border-radius: 12px; background: #fff; border: 1px solid var(--kp-line); color: var(--kp-navy); font-weight: 600; font-size: .85rem; text-decoration: none; }
    .hd-actions a i { color: var(--kp-gold); font-size: 1.1rem; }
    .hd-actions a.main { background: var(--kp-navy); color: #fff; border-color: var(--kp-navy); }
    .hd-stats { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; margin-bottom: 14px; }
    .hd-stat { background: #fff; border: 1px solid var(--kp-line); border-radius: 14px; padding: 12px 14px; text-decoration: none; display: block; position: relative; overflow: hidden; }
    .hd-stat .l { font-size: .74rem; color: var(--kp-muted); font-weight: 600; }
    .hd-stat .v { font-size: 1.6rem; font-weight: 800; color: var(--kp-navy); line-height: 1.2; }
    .hd-stat .s { font-size: .72rem; color: var(--kp-muted); }
    .hd-stat::after { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--c, var(--kp-gold)); }
    .hd-grid { display: grid; gap: 14px; }
    .hd-grid .kp-panel { margin-bottom: 0; }
    .hd-av { border-radius: 50%; background: #eef1f6 center/cover no-repeat; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: .75rem; color: var(--kp-navy); flex-shrink: 0; }
    .hd-row { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid var(--kp-line); text-decoration: none; color: inherit; }
    .hd-row:last-child { border-bottom: 0; }
    .hd-row .bd { flex: 1; min-width: 0; }
    .hd-row .t { font-weight: 700; color: var(--kp-navy); font-size: .9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .hd-row .m { font-size: .76rem; color: var(--kp-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .hd-tag { font-size: .7rem; font-weight: 700; padding: 3px 9px; border-radius: 999px; background: #eef1f6; color: var(--kp-muted); white-space: nowrap; }
    .hd-tag.gold { background: var(--kp-gold); color: var(--kp-navy); }
    .hd-tag.red { background: #fde8e8; color: #b42318; }
    .hd-tag.green { background: #e7f6ec; color: #1a7f3c; }
    .hd-bar { margin: 8px 0; }
    .hd-bar .l { display: flex; justify-content: space-between; font-size: .82rem; gap: 8px; }
    .hd-bar .l span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--kp-text); }
    .hd-bar .l b { color: var(--kp-navy); }
    .hd-bar .t { height: 7px; border-radius: 99px; background: #eef1f6; margin-top: 3px; overflow: hidden; }
    .hd-bar .t i { display: block; height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--kp-navy), #2c4d86); }
    .hd-more { font-size: .75rem; color: var(--kp-muted); margin-top: 4px; }
    .hd-tabs { display: flex; gap: 6px; margin: 4px 0 6px; flex-wrap: wrap; }
    .hd-tabs button { border: 1px solid var(--kp-line); background: #fff; border-radius: 999px; font-size: .75rem; font-weight: 600; color: var(--kp-muted); padding: 4px 10px; }
    .hd-tabs button.on { background: var(--kp-navy); border-color: var(--kp-navy); color: #fff; }
    .hd-pane { display: none; } .hd-pane.on { display: block; }
    .hd-money { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; margin: 8px 0; }
    .hd-money div { background: #f6f8fb; border-radius: 10px; padding: 8px 10px; }
    .hd-money .l { font-size: .7rem; color: var(--kp-muted); font-weight: 600; }
    .hd-money .v { font-weight: 800; color: var(--kp-navy); }
    .hd-gaps details { border-bottom: 1px solid var(--kp-line); padding: 8px 0; }
    .hd-gaps details:last-child { border-bottom: 0; }
    .hd-gaps summary { display: flex; justify-content: space-between; align-items: center; cursor: pointer; list-style: none; font-size: .88rem; font-weight: 600; color: var(--kp-text); }
    .hd-gaps summary::-webkit-details-marker { display: none; }
    .hd-gaps .names { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
    .hd-gaps .names a { font-size: .76rem; padding: 3px 9px; border-radius: 999px; background: #f6f8fb; color: var(--kp-navy); text-decoration: none; }
    @media (min-width: 768px) { .hd-stats { grid-template-columns: repeat(3, minmax(0, 1fr)); } .hd-grid.two { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (min-width: 1200px) { .hd-stats { grid-template-columns: repeat(6, minmax(0, 1fr)); } .hd-grid.three { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
</style>
<div class="container-xxl flex-grow-1 container-p-y kp" style="max-width:1300px">
    <div class="hr-head">
        <div><h4>KeyPeople</h4><div class="sub">HR at a glance · {{ $today->format('l, d M Y') }}</div></div>
    </div>

    <div class="hd-actions">
        <a href="{{ route('users.index') }}" class="main"><i class="bx bx-group"></i> Employees</a>
        @can('user-create')<a href="{{ route('users.create') }}"><i class="bx bx-user-plus"></i> Add employee</a>@endcan
        @if($leave)<a href="{{ route('leave.hr') }}"><i class="bx bx-calendar"></i> Leave @if($leave['pending'])<span class="hd-tag gold">{{ $leave['pending'] }}</span>@endif</a>@endif
        @if($letters)<a href="{{ route('letters.create') }}"><i class="bx bx-file-blank"></i> Issue a letter</a>@endif
        <a href="{{ route('celebrations.index') }}"><i class="bx bx-cake"></i> Celebrations</a>
        @if($salary)<a href="{{ route('payslip.register') }}"><i class="bx bx-spreadsheet"></i> Salary register</a>@endif
        @can('user-org-tree-view')<a href="{{ url('org_tree') }}"><i class="bx bx-sitemap"></i> Org tree</a>@endcan
        @if($leave)<a href="{{ route('leave.setup') }}"><i class="bx bx-cog"></i> Leave setup</a>@endif
    </div>

    <div class="hd-stats">
        <a class="hd-stat" href="{{ route('users.index') }}" style="--c:#011e48"><div class="l">Active employees</div><div class="v">{{ number_format($head['active']) }}</div><div class="s">{{ $head['probation'] }} on probation</div></a>
        <a class="hd-stat" href="#joiners" style="--c:#1a7f3c"><div class="l">Joined this month</div><div class="v">{{ $head['joined'] }}</div><div class="s">{{ $today->format('F') }}</div></a>
        <a class="hd-stat" href="#leaving" style="--c:#b42318"><div class="l">Left this month</div><div class="v">{{ $head['left'] }}</div><div class="s">{{ $noticePeriod->count() }} leaving soon</div></a>
        @if($leave)
            <a class="hd-stat" href="#today" style="--c:#c3b246"><div class="l">On leave today</div><div class="v">{{ $leave['today']->count() }}</div><div class="s">{{ $leave['week']->count() }} more this week</div></a>
            <a class="hd-stat" href="{{ route('leave.hr') }}" style="--c:#e6a100"><div class="l">Leave to approve</div><div class="v">{{ $leave['pending'] }}</div><div class="s">pending requests</div></a>
        @endif
        <a class="hd-stat" href="#probation" style="--c:#6f42c1"><div class="l">Probation ending</div><div class="v">{{ $probationDue->count() }}</div><div class="s">in 30 days or overdue</div></a>
    </div>

    <div class="hd-grid two mb-3">
        {{-- Today --}}
        <div class="kp-panel" id="today">
            <div class="kp-panel-h"><h2><i class="bx bx-sun"></i>Today</h2>@if($leave)<a class="kp-note" href="{{ route('leave.hr') }}">All leave</a>@endif</div>
            <div class="kp-panel-b">
                @if($leave)
                    @forelse($leave['today'] as $r)
                        <a class="hd-row" href="{{ route('users.show', $r->user_id) }}">
                            {!! $avatar($r->user) !!}
                            <div class="bd"><div class="t">{{ $r->user?->name }}</div><div class="m">{{ $r->type?->name ?? 'Leave' }} · till {{ $r->to_date->format('d M') }}{{ $r->half_day ? ' · half day' : '' }}</div></div>
                            <span class="hd-tag">On leave</span>
                        </a>
                    @empty
                        <div class="kp-empty">Everyone is in today.</div>
                    @endforelse
                    @if($leave['week']->count())
                        <div class="hr-count mt-2 mb-1" style="font-size:.8rem">Coming up this week</div>
                        @foreach($leave['week'] as $r)
                            <a class="hd-row" href="{{ route('users.show', $r->user_id) }}">
                                {!! $avatar($r->user, 30) !!}
                                <div class="bd"><div class="t">{{ $r->user?->name }}</div><div class="m">{{ $r->type?->name ?? 'Leave' }} · {{ $r->from_date->format('D d M') }}{{ $r->to_date->ne($r->from_date) ? ' – '.$r->to_date->format('d M') : '' }}</div></div>
                            </a>
                        @endforeach
                    @endif
                    @if($leave['holidays']->count())
                        <div class="hr-count mt-2 mb-1" style="font-size:.8rem">Next holidays</div>
                        @foreach($leave['holidays'] as $h)
                            <div class="hd-row"><span class="hd-av" style="width:30px;height:30px;border-radius:8px"><i class="bx bx-calendar-star"></i></span><div class="bd"><div class="t">{{ $h->name }}</div><div class="m">{{ $h->date->format('l, d M') }}</div></div></div>
                        @endforeach
                    @endif
                @else
                    <div class="kp-empty">Leave isn't set up yet.</div>
                @endif
            </div>
        </div>

        {{-- Celebrations --}}
        <div class="kp-panel">
            <div class="kp-panel-h"><h2><i class="bx bx-cake"></i>Birthdays and anniversaries</h2><a class="kp-note" href="{{ route('celebrations.index') }}">Next 14 days</a></div>
            <div class="kp-panel-b">
                @forelse($celebrations->take(8) as $it)
                    @php $u = $it['user']; $isPosted = in_array($u->id.'|'.$it['type'].'|'.$it['date']->toDateString(), $posted, true); @endphp
                    <div class="hd-row">
                        {!! $avatar($u) !!}
                        <div class="bd">
                            <div class="t">{{ $it['type'] === 'birthday' ? '🎂' : '🏆' }} {{ $u->name }}</div>
                            <div class="m">{{ $it['type'] === 'birthday' ? 'Birthday' : $it['years'].CS::suffix($it['years']).' work anniversary' }} · {{ $it['days'] === 0 ? 'Today' : ($it['days'] === 1 ? 'Tomorrow' : $it['date']->format('D d M')) }}</div>
                        </div>
                        @if($isPosted)
                            <span class="hd-tag green">Posted</span>
                        @else
                            <a class="btn btn-sm {{ $it['days'] === 0 ? 'btn-kp' : 'btn-outline-primary' }}" href="{{ route('celebrations.make', ['user_id' => $u->id, 'type' => $it['type'], 'date' => $it['date']->toDateString()]) }}">Post</a>
                        @endif
                    </div>
                @empty
                    <div class="kp-empty">No birthdays or anniversaries in the next 14 days.</div>
                @endforelse
                @if($celebrations->count() > 8)<div class="hd-more">+ {{ $celebrations->count() - 8 }} more on the Celebrations page</div>@endif
            </div>
        </div>
    </div>

    <div class="hd-grid three mb-3">
        <div class="kp-panel" id="probation">
            <div class="kp-panel-h"><h2><i class="bx bx-time-five"></i>Probation ending</h2><span class="kp-note">confirm or extend</span></div>
            <div class="kp-panel-b">
                @forelse($probationDue->take(8) as $u)
                    @php $d = (int) $today->diffInDays($u->probation_end, false); @endphp
                    <a class="hd-row" href="{{ route('users.edit', $u->id) }}">
                        {!! $avatar($u) !!}
                        <div class="bd"><div class="t">{{ $u->name }}</div><div class="m">{{ $u->designation?->name ?? '—' }} · joined {{ F::date($u->joining_date) }}</div></div>
                        <span class="hd-tag {{ $d < 0 ? 'red' : 'gold' }}">{{ $d < 0 ? 'Overdue '.abs($d).'d' : ($d === 0 ? 'Today' : $u->probation_end->format('d M')) }}</span>
                    </a>
                @empty
                    <div class="kp-empty">No probation ending in the next 30 days.</div>
                @endforelse
                @if($probationDue->count() > 8)<div class="hd-more">+ {{ $probationDue->count() - 8 }} more</div>@endif
            </div>
        </div>

        <div class="kp-panel" id="joiners">
            <div class="kp-panel-h"><h2><i class="bx bx-user-plus"></i>New joiners</h2><span class="kp-note">last 30 days</span></div>
            <div class="kp-panel-b">
                @forelse($joiners as $u)
                    <a class="hd-row" href="{{ route('users.show', $u->id) }}">
                        {!! $avatar($u) !!}
                        <div class="bd"><div class="t">{{ $u->name }}</div><div class="m">{{ $u->designation?->name ?? '—' }}</div></div>
                        <span class="hd-tag green">{{ F::date($u->joining_date, 'd M') }}</span>
                    </a>
                @empty
                    <div class="kp-empty">No one joined in the last 30 days.</div>
                @endforelse
            </div>
        </div>

        <div class="kp-panel" id="leaving">
            <div class="kp-panel-h"><h2><i class="bx bx-log-out"></i>Leaving soon</h2><span class="kp-note">notice period</span></div>
            <div class="kp-panel-b">
                @forelse($noticePeriod as $u)
                    <a class="hd-row" href="{{ route('users.show', $u->id) }}">
                        {!! $avatar($u) !!}
                        <div class="bd"><div class="t">{{ $u->name }}</div><div class="m">{{ $u->designation?->name ?? '—' }}{{ $u->fnf_status ? ' · F&F '.strtolower($u->fnf_status) : '' }}</div></div>
                        <span class="hd-tag red">{{ F::date($u->leaving_date, 'd M') }}</span>
                    </a>
                @empty
                    <div class="kp-empty">No one is serving notice.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="hd-grid {{ $salary ? 'three' : 'two' }} mb-3">
        <div class="kp-panel">
            <div class="kp-panel-h"><h2><i class="bx bx-pie-chart-alt-2"></i>Team mix</h2><span class="kp-note">{{ $head['active'] }} active</span></div>
            <div class="kp-panel-b">
                <div class="hd-tabs" data-tabs>
                    <button type="button" class="on" data-pane="mix-co">Company</button>
                    <button type="button" data-pane="mix-dept">Department</button>
                    <button type="button" data-pane="mix-loc">Location</button>
                    <button type="button" data-pane="mix-gender">Gender</button>
                </div>
                <div class="hd-pane on" id="mix-co">{!! $bars($byCompany) !!}</div>
                <div class="hd-pane" id="mix-dept">{!! $bars($byDept) !!}</div>
                <div class="hd-pane" id="mix-loc">{!! $bars($byLocation) !!}</div>
                <div class="hd-pane" id="mix-gender">{!! $bars($gender) !!}</div>
            </div>
        </div>

        @if($salary)
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-rupee"></i>Salary · {{ $salary['month']->format('F Y') }}</h2><a class="kp-note" href="{{ route('payslip.register', ['fy' => $salary['fy'], 'month' => $salary['month']->month]) }}">Register</a></div>
                <div class="kp-panel-b">
                    <div class="hd-money">
                        <div><div class="l">Net paid</div><div class="v">{{ F::inr($salary['net']) }}</div></div>
                        <div><div class="l">People paid</div><div class="v">{{ $salary['paid'] }}</div></div>
                        <div><div class="l">Gross</div><div class="v">{{ F::inr($salary['gross']) }}</div></div>
                        <div><div class="l">Total cost</div><div class="v">{{ F::inr($salary['cost']) }}</div></div>
                    </div>
                    @if($salary['missing'])<div class="hd-row"><i class="bx bx-error-circle text-warning fs-5"></i><div class="bd"><div class="t" style="font-weight:600">{{ $salary['missing'] }} active {{ $salary['missing'] === 1 ? 'person has' : 'people have' }} no salary saved for {{ $salary['month']->format('F') }}</div></div></div>@endif
                    @if($salary['no_bank'])<div class="hd-row"><i class="bx bx-credit-card text-danger fs-5"></i><div class="bd"><div class="t" style="font-weight:600">{{ $salary['no_bank'] }} without bank account or IFSC</div></div></div>@endif
                </div>
            </div>
        @endif

        <div class="kp-panel">
            <div class="kp-panel-h"><h2><i class="bx bx-envelope"></i>Letters</h2>@if($letters)<a class="kp-note" href="{{ route('letters.index') }}">All letters</a>@endif</div>
            <div class="kp-panel-b">
                @if($letters)
                    <div class="hd-money"><div><div class="l">Issued this month</div><div class="v">{{ $letters['month'] }}</div></div><div><div class="l">Signature</div><div class="v" style="font-size:.9rem">{!! $letters['signature'] ? '<span class="text-success">Set up</span>' : '<a href="'.route('letters.signatory.edit').'">Add one</a>' !!}</div></div></div>
                    @foreach($letters['recent'] as $l)
                        <a class="hd-row" target="_blank" href="{{ route('letters.pdf', $l) }}">
                            <span class="hd-av" style="width:30px;height:30px;border-radius:8px"><i class="bx bx-file"></i></span>
                            <div class="bd"><div class="t">{{ $l->subject ?: ucfirst($l->type).' letter' }}</div><div class="m">{{ $l->user?->name }} · {{ $l->letter_date->format('d M') }}</div></div>
                        </a>
                    @endforeach
                @else
                    <div class="kp-empty">Letters aren't set up yet.</div>
                @endif
            </div>
        </div>
    </div>

    @if($gaps->count())
        <div class="kp-panel hd-gaps">
            <div class="kp-panel-h"><h2><i class="bx bx-error"></i>Profiles missing details</h2><span class="kp-note">active people · tap to see who</span></div>
            <div class="kp-panel-b">
                @foreach($gaps as $g)
                    <details>
                        <summary><span>{{ $g['label'] }}</span><span class="hd-tag {{ $g['people']->count() > 5 ? 'red' : 'gold' }}">{{ $g['people']->count() }}</span></summary>
                        <div class="names">
                            @foreach($g['people']->take(40) as $u)<a href="{{ route('users.edit', $u->id) }}">{{ $u->name }}</a>@endforeach
                            @if($g['people']->count() > 40)<span class="small text-muted">+ {{ $g['people']->count() - 40 }} more</span>@endif
                        </div>
                    </details>
                @endforeach
            </div>
        </div>
    @endif
</div>
<script>
document.querySelectorAll('[data-tabs]').forEach(function (tabs) {
    tabs.addEventListener('click', function (e) {
        var b = e.target.closest('button'); if (!b) return;
        tabs.querySelectorAll('button').forEach(function (x) { x.classList.toggle('on', x === b); });
        b.closest('.kp-panel-b').querySelectorAll('.hd-pane').forEach(function (p) { p.classList.toggle('on', p.id === b.dataset.pane); });
    });
});
</script>
@endsection

@extends('layouts.app')

@php
    use App\Support\PeopleFormat as F;
    use App\Services\CelebrationService as CS;
    $avatar = function ($u, $size = 38) {
        $photo = $u && $u->photo && is_file(public_path('storage/'.$u->photo)) ? asset('storage/'.$u->photo) : null;
        return '<span class="me-av" style="width:'.$size.'px;height:'.$size.'px;'.($photo ? "background-image:url('".e($photo)."')" : '').'">'.($photo ? '' : e(F::initials($u?->name))).'</span>';
    };
    $d = fn ($n) => rtrim(rtrim(number_format((float) $n, 1), '0'), '.');
    $hour = (int) now()->format('G');
    $hello = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $first = explode(' ', trim((string) $me->name))[0] ?? '';
@endphp

@section('content')
@include('hr.partials.hr-styles')
<style>
    .me-hero { display: flex; align-items: center; gap: 14px; background: linear-gradient(135deg, var(--kp-navy), #1d3a6b); color: #fff; border-radius: 18px; padding: 16px 18px; margin-bottom: 14px; }
    .me-hero .me-av { border: 2px solid var(--kp-gold); color: var(--kp-navy); }
    .me-hero h4 { color: #fff; margin: 0; font-weight: 800; font-size: 1.2rem; }
    .me-hero .s { font-size: .8rem; opacity: .85; }
    .me-wish { background: #fff8e1; border: 1px solid #f2d98a; color: #6b5400; border-radius: 14px; padding: 10px 14px; margin-bottom: 14px; font-weight: 600; font-size: .9rem; }
    .me-actions { display: flex; gap: 8px; overflow-x: auto; scrollbar-width: none; padding-bottom: 2px; margin-bottom: 14px; }
    .me-actions::-webkit-scrollbar { display: none; }
    .me-actions a { white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; padding: 9px 13px; border-radius: 12px; background: #fff; border: 1px solid var(--kp-line); color: var(--kp-navy); font-weight: 600; font-size: .85rem; text-decoration: none; }
    .me-actions a i { color: var(--kp-gold); font-size: 1.1rem; }
    .me-actions a.main { background: var(--kp-navy); color: #fff; border-color: var(--kp-navy); }
    .me-bal { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; margin-bottom: 14px; }
    .me-bal a { background: #fff; border: 1px solid var(--kp-line); border-radius: 14px; padding: 12px 14px; text-decoration: none; display: block; position: relative; overflow: hidden; }
    .me-bal a::after { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--c, var(--kp-gold)); }
    .me-bal .l { font-size: .74rem; color: var(--kp-muted); font-weight: 600; }
    .me-bal .v { font-size: 1.5rem; font-weight: 800; color: var(--kp-navy); line-height: 1.2; }
    .me-bal .v small { font-size: .72rem; font-weight: 600; color: var(--kp-muted); }
    .me-bal .s { font-size: .72rem; color: var(--kp-muted); }
    .me-grid { display: grid; gap: 14px; }
    .me-grid .kp-panel { margin-bottom: 0; }
    .me-av { border-radius: 50%; background: #eef1f6 center/cover no-repeat; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: .75rem; color: var(--kp-navy); flex-shrink: 0; }
    .me-row { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid var(--kp-line); text-decoration: none; color: inherit; }
    .me-row:last-child { border-bottom: 0; }
    .me-row .bd { flex: 1; min-width: 0; }
    .me-row .t { font-weight: 700; color: var(--kp-navy); font-size: .9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .me-row .m { font-size: .76rem; color: var(--kp-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .me-ic { width: 34px; height: 34px; border-radius: 10px; background: #f6f8fb; display: inline-flex; align-items: center; justify-content: center; color: var(--kp-navy); font-size: 1.1rem; flex-shrink: 0; }
    .me-tag { font-size: .7rem; font-weight: 700; padding: 3px 9px; border-radius: 999px; background: #eef1f6; color: var(--kp-muted); white-space: nowrap; }
    .me-tag.gold { background: var(--kp-gold); color: var(--kp-navy); }
    .me-tag.green { background: #e7f6ec; color: #1a7f3c; }
    .me-gaps { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
    .me-gaps span { font-size: .76rem; padding: 3px 9px; border-radius: 999px; background: #fde8e8; color: #b42318; font-weight: 600; }
    @media (min-width: 768px) { .me-bal { grid-template-columns: repeat(4, minmax(0, 1fr)); } .me-grid.two { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (min-width: 1200px) { .me-grid.three { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
</style>
<div class="container-xxl flex-grow-1 container-p-y kp" style="max-width:1200px">
    <div class="me-hero">
        {!! $avatar($me, 54) !!}
        <div style="min-width:0">
            <h4>{{ $hello }}, {{ $first }}</h4>
            <div class="s">{{ $me->designation?->name ?? 'KeyMe' }}{{ $manager ? ' · reports to '.$manager->name : '' }}</div>
            <div class="s">{{ $today->format('l, d M Y') }}</div>
        </div>
    </div>

    @if($mine)
        <div class="me-wish">{{ $mine['type'] === 'birthday' ? '🎂 Happy birthday from all of us!' : '🏆 Happy '.$mine['years'].CS::suffix($mine['years']).' work anniversary! Thank you for being with us.' }}</div>
    @endif

    <div class="me-actions">
        @if($leave)<a href="{{ route('leave.my') }}#applyForm" class="main"><i class="bx bx-calendar-plus"></i> Apply for leave</a>@endif
        @if($leave && $leave['teamPendingCount'])<a href="{{ route('leave.team') }}"><i class="bx bx-check-double"></i> Approve team leave <span class="me-tag gold">{{ $leave['teamPendingCount'] }}</span></a>@endif
        <a href="{{ route('payslip.mine') }}"><i class="bx bx-receipt"></i> My payslips</a>
        <a href="{{ route('letters.mine') }}"><i class="bx bx-envelope"></i> My letters</a>
        @can('module-keyearn')<a href="{{ route('my-incentive') }}"><i class="bx bx-rupee"></i> My incentive</a>@endcan
        @if(\Illuminate\Support\Facades\Route::has('users.show'))@can('user-view')<a href="{{ route('users.show', $me->id) }}"><i class="bx bx-id-card"></i> My profile</a>@endcan @endif
    </div>

    @if($leave)
        <div class="me-bal">
            @foreach($leave['balances'] as $b)
                <a href="{{ route('leave.my') }}" style="--c: {{ $b['type']->color ?: 'var(--kp-gold)' }}">
                    <div class="l">{{ $b['type']->name }}</div>
                    @if($b['type']->is_paid)
                        <div class="v">{{ $d($b['available']) }} <small>left</small></div>
                        <div class="s">of {{ $d($b['quota'] + $b['adjusted']) }} · used {{ $d($b['used']) }}</div>
                    @else
                        <div class="v">{{ $d($b['used']) }} <small>taken</small></div>
                        <div class="s">unpaid</div>
                    @endif
                </a>
            @endforeach
        </div>
    @endif

    @if($leave && ($leave['teamPendingCount'] || $leave['teamOff']->count()))
        <div class="kp-panel mb-3">
            <div class="kp-panel-h"><h2><i class="bx bx-group"></i>My team</h2><a class="kp-note" href="{{ route('leave.team') }}">Team leave</a></div>
            <div class="kp-panel-b">
                @foreach($leave['teamPending'] as $r)
                    <a class="me-row" href="{{ route('leave.team') }}">
                        {!! $avatar($r->user) !!}
                        <div class="bd"><div class="t">{{ $r->user?->name }}</div><div class="m">{{ $r->type?->name ?? 'Leave' }} · {{ $r->from_date->format('d M') }}{{ $r->to_date->ne($r->from_date) ? ' – '.$r->to_date->format('d M') : '' }}</div></div>
                        <span class="me-tag gold">To approve</span>
                    </a>
                @endforeach
                @if($leave['teamOff']->count())
                    <div class="hr-count mt-2 mb-1" style="font-size:.8rem">On leave today</div>
                    @foreach($leave['teamOff'] as $r)
                        <div class="me-row">{!! $avatar($r->user, 30) !!}<div class="bd"><div class="t">{{ $r->user?->name }}</div><div class="m">{{ $r->type?->name ?? 'Leave' }} · till {{ $r->to_date->format('d M') }}</div></div></div>
                    @endforeach
                @endif
            </div>
        </div>
    @endif

    <div class="me-grid two mb-3">
        <div class="kp-panel">
            <div class="kp-panel-h"><h2><i class="bx bx-calendar-event"></i>My leave</h2>@if($leave)<span class="kp-note">approver {{ $leave['approver']?->name ?? 'HR' }}</span>@endif</div>
            <div class="kp-panel-b">
                @if($leave)
                    @forelse($leave['upcoming'] as $r)
                        <a class="me-row" href="{{ route('leave.my') }}">
                            <span class="me-ic"><i class="bx bx-calendar"></i></span>
                            <div class="bd"><div class="t">{{ $r->type?->name ?? 'Leave' }} · {{ $d($r->days) }} {{ (float) $r->days === 1.0 ? 'day' : 'days' }}</div><div class="m">{{ $r->from_date->format('D d M') }}{{ $r->to_date->ne($r->from_date) ? ' – '.$r->to_date->format('D d M') : '' }}</div></div>
                            <span class="me-tag {{ $r->status === 'approved' ? 'green' : 'gold' }}">{{ $r->status === 'approved' ? 'Approved' : 'Waiting' }}</span>
                        </a>
                    @empty
                        <div class="kp-empty">No leave coming up.</div>
                    @endforelse
                    @if($leave['holidays']->count())
                        <div class="hr-count mt-2 mb-1" style="font-size:.8rem">Next holidays</div>
                        @foreach($leave['holidays'] as $h)
                            <div class="me-row"><span class="me-ic"><i class="bx bx-calendar-star"></i></span><div class="bd"><div class="t">{{ $h->name }}</div><div class="m">{{ $h->date->format('l, d M') }}</div></div></div>
                        @endforeach
                    @endif
                @else
                    <div class="kp-empty">Leave isn't set up yet.</div>
                @endif
            </div>
        </div>

        <div class="kp-panel">
            <div class="kp-panel-h"><h2><i class="bx bx-cake"></i>Celebrations this week</h2></div>
            <div class="kp-panel-b">
                    @forelse($celebrations as $it)
                        <div class="me-row">
                            {!! $avatar($it['user']) !!}
                            <div class="bd"><div class="t">{{ $it['type'] === 'birthday' ? '🎂' : '🏆' }} {{ $it['user']->name }}</div><div class="m">{{ $it['type'] === 'birthday' ? 'Birthday' : $it['years'].CS::suffix($it['years']).' work anniversary' }} · {{ $it['days'] === 0 ? 'Today' : ($it['days'] === 1 ? 'Tomorrow' : $it['date']->format('D d M')) }}</div></div>
                        </div>
                    @empty
                        <div class="kp-empty">No birthdays or anniversaries this week.</div>
                    @endforelse
            </div>
        </div>
    </div>

    <div class="me-grid three">
        <div class="kp-panel">
            <div class="kp-panel-h"><h2><i class="bx bx-receipt"></i>Payslips</h2><a class="kp-note" href="{{ route('payslip.mine') }}">All</a></div>
            <div class="kp-panel-b">
                @forelse($payslips as $r)
                    <div class="me-row">
                        <span class="me-ic"><i class="bx bx-rupee"></i></span>
                        <div class="bd"><div class="t">{{ $r->date->format('F Y') }}</div><div class="m">Credited ₹{{ number_format((float) $r->salary_credited) }}</div></div>
                        <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('payslip.pdf', [$me->id, $r->financial_year, $r->month]) }}">Open</a>
                    </div>
                @empty
                    <div class="kp-empty">No payslips yet.</div>
                @endforelse
            </div>
        </div>

        <div class="kp-panel">
            <div class="kp-panel-h"><h2><i class="bx bx-envelope"></i>Letters</h2><a class="kp-note" href="{{ route('letters.mine') }}">All</a></div>
            <div class="kp-panel-b">
                @forelse($letters as $l)
                    <div class="me-row">
                        <span class="me-ic"><i class="bx bx-file"></i></span>
                        <div class="bd"><div class="t">{{ $l->subject ?: ucfirst($l->type).' letter' }}</div><div class="m">{{ $l->letter_date->format('d M Y') }}</div></div>
                        <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('letters.pdf', $l) }}">Open</a>
                    </div>
                @empty
                    <div class="kp-empty">No letters yet.</div>
                @endforelse
            </div>
        </div>

        <div class="kp-panel">
            <div class="kp-panel-h"><h2><i class="bx bx-user-check"></i>My profile</h2></div>
            <div class="kp-panel-b">
                    <div class="me-row"><span class="me-ic"><i class="bx bx-briefcase"></i></span><div class="bd"><div class="t">{{ $me->designation?->name ?? '—' }}</div><div class="m">{{ $me->department?->name ?? 'Department not set' }}{{ $me->location?->name ? ' · '.$me->location->name : '' }}</div></div></div>
                    <div class="me-row"><span class="me-ic"><i class="bx bx-calendar-check"></i></span><div class="bd"><div class="t">Joined {{ $me->joining_date ? F::date($me->joining_date) : '—' }}</div><div class="m">{{ $me->employee_code ? 'Employee code '.$me->employee_code : '' }}</div></div></div>
                    @if($gaps->count())
                        <div class="mt-2" style="font-size:.82rem;color:var(--kp-muted)">Missing in your profile. Please share these with HR:</div>
                        <div class="me-gaps">@foreach($gaps as $g)<span>{{ $g }}</span>@endforeach</div>
                    @else
                        <div class="mt-2"><span class="me-tag green">Profile complete</span></div>
                    @endif
            </div>
        </div>
    </div>
</div>
@endsection

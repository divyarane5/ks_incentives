@extends('layouts.app')

@section('content')
@include('hr.partials.hr-styles')
<style>
    .cel-row { display: flex; align-items: center; gap: 12px; padding: 11px 0; border-bottom: 1px solid var(--kp-line); }
    .cel-row:last-child { border-bottom: 0; }
    .cel-av { width: 46px; height: 46px; border-radius: 50%; background: #eef1f6 center/cover; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--kp-navy); flex-shrink: 0; position: relative; }
    .cel-av .tag { position: absolute; right: -4px; bottom: -4px; width: 22px; height: 22px; border-radius: 50%; background: var(--kp-gold); color: var(--kp-navy); display: flex; align-items: center; justify-content: center; font-size: 13px; border: 2px solid #fff; }
    .cel-row .bd { flex: 1; min-width: 0; }
    .cel-row .t { font-weight: 700; color: var(--kp-navy); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .cel-row .m { font-size: .8rem; color: var(--kp-muted); }
    .cel-when { font-size: .72rem; font-weight: 700; padding: 3px 9px; border-radius: 999px; background: #eef1f6; color: var(--kp-muted); white-space: nowrap; }
    .cel-when.today { background: var(--kp-gold); color: var(--kp-navy); }
    .cel-done { font-size: .72rem; font-weight: 700; color: #1a7f3c; white-space: nowrap; }
</style>
<div class="container-xxl flex-grow-1 container-p-y kp">
    <div class="hr-head">
        <div><h4>Celebrations</h4><div class="sub">Birthdays and work anniversaries in the next 30 days. Make a post for Home, or download it for WhatsApp.</div></div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="hr-pills">
        <a href="{{ route('celebrations.index') }}" class="{{ ! $type ? 'on' : '' }}">All</a>
        <a href="{{ route('celebrations.index', ['type' => 'birthday']) }}" class="{{ $type === 'birthday' ? 'on' : '' }}">🎂 Birthdays</a>
        <a href="{{ route('celebrations.index', ['type' => 'anniversary']) }}" class="{{ $type === 'anniversary' ? 'on' : '' }}">🏆 Work anniversaries</a>
    </div>

    <div class="kp-panel">
        <div class="kp-panel-h"><h2><i class="bx bx-cake"></i> Coming up</h2><span class="kp-note">{{ $items->count() }} in 30 days</span></div>
        <div class="kp-panel-b">
            @forelse($items as $it)
                @php
                    $u = $it['user'];
                    $photo = $u->photo && is_file(public_path('storage/'.$u->photo)) ? asset('storage/'.$u->photo) : null;
                    $post = $posted[$u->id.'|'.$it['type'].'|'.$it['date']->toDateString()] ?? null;
                    $when = $it['days'] === 0 ? 'Today' : ($it['days'] === 1 ? 'Tomorrow' : $it['date']->format('D, d M'));
                @endphp
                <div class="cel-row">
                    <div class="cel-av" @if($photo) style="background-image:url('{{ $photo }}')" @endif>
                        @unless($photo){{ \App\Support\PeopleFormat::initials($u->name) }}@endunless
                        <span class="tag">{{ $it['type'] === 'birthday' ? '🎂' : '🏆' }}</span>
                    </div>
                    <div class="bd">
                        <div class="t">{{ $u->name }}</div>
                        <div class="m">
                            {{ $it['type'] === 'birthday' ? 'Birthday' : \App\Services\CelebrationService::yearsWord($it['years']).' work anniversary' }}
                            @if($u->designation) · {{ $u->designation->name }}@endif
                        </div>
                        <div class="mt-1 d-flex align-items-center gap-2 flex-wrap">
                            <span class="cel-when {{ $it['days'] === 0 ? 'today' : '' }}">{{ $when }}</span>
                            @if($post)<span class="cel-done"><i class="bx bx-check-circle"></i> {{ $it['days'] === 0 ? 'On Home' : 'Scheduled' }}</span>@endif
                        </div>
                    </div>
                    <a class="btn btn-sm {{ $post ? 'btn-outline-primary' : 'btn-kp' }}" href="{{ route('celebrations.make', ['user_id' => $u->id, 'type' => $it['type'], 'date' => $it['date']->toDateString()]) }}">
                        {{ $post ? 'Edit' : 'Make post' }}
                    </a>
                </div>
            @empty
                <div class="kp-empty">Nothing in the next 30 days. Birthdays need a date of birth on the profile.</div>
            @endforelse
        </div>
    </div>

    <div class="kp-panel">
        <div class="kp-panel-h"><h2><i class="bx bx-images"></i> Posts</h2><span class="kp-note">last 30 days and scheduled</span></div>
        <div class="kp-panel-b">
            @forelse($posts as $p)
                <div class="cel-row">
                    <div class="bd">
                        <div class="t">{{ $p->user?->name ?? 'Removed person' }}</div>
                        <div class="m">{{ $p->type === 'birthday' ? 'Birthday' : 'Work anniversary' }} · {{ $p->post_date->format('d M Y') }} · Design {{ $p->design }}</div>
                    </div>
                    @if($p->user)
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('celebrations.make', ['user_id' => $p->user_id, 'type' => $p->type, 'date' => $p->post_date->toDateString()]) }}">Open</a>
                    @endif
                    <form method="POST" action="{{ route('celebrations.destroy', $p) }}" onsubmit="return confirm('Remove this post from the Home page?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Remove"><i class="bx bx-trash"></i></button>
                    </form>
                </div>
            @empty
                <div class="kp-empty">No posts yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

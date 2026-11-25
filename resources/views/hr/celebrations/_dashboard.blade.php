{{-- Home page: today's birthday / anniversary posts for everyone, and a nudge for HR. --}}
@php
    $celPosts = collect(); $celTodo = 0; $celHr = false;
    if (\Illuminate\Support\Facades\Schema::hasTable('celebration_posts')) {
        $celPosts = \App\Models\CelebrationPost::with(['user.designation', 'user.businessUnit'])
            ->whereDate('post_date', now()->toDateString())->orderBy('type')->get()->filter(fn ($p) => $p->user);
        $celHr = \App\Services\LeaveService::isHr(auth()->user());
        if ($celHr) {
            $celTodo = \App\Services\CelebrationService::upcoming(0)
                ->reject(fn ($it) => $celPosts->contains(fn ($p) => $p->user_id === $it['user']->id && $p->type === $it['type']))->count();
        }
    }
@endphp
@if($celPosts->count() || $celTodo)
<div class="rs-sec">
    <div class="rs-sec-head">
        <i class="bx bx-cake" style="color:#c3b246"></i>
        <div><h5>Celebrations today</h5><div class="rs-sub">Birthdays and work anniversaries</div></div>
        @if($celHr)<a class="rs-open" href="{{ route('celebrations.index') }}">Manage</a>@endif
    </div>
    @if($celTodo)
        <div class="alert alert-warning d-flex align-items-center gap-2 py-2">
            <i class="bx bx-party"></i>
            <div class="flex-fill">{{ $celTodo }} {{ $celTodo === 1 ? 'celebration' : 'celebrations' }} today without a post yet.</div>
            <a href="{{ route('celebrations.index') }}" class="btn btn-sm btn-warning">Make post</a>
        </div>
    @endif
    @if($celPosts->count())
    <div class="row g-3">
        @foreach($celPosts as $p)
            @php
                $c = \App\Services\CelebrationService::card($p->type, (int) $p->design, $p->user, $p->years, $p->message, $p->post_date, $p->photo, $p->photo_pos);
                $wa = \App\Support\PeopleFormat::intlPhone($p->user->official_contact ?: $p->user->personal_contact);
            @endphp
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="cel-fit">@include('hr.celebrations._card', ['c' => $c])</div>
                <div class="d-flex gap-2 mt-2">
                    @if($wa && $p->user_id !== auth()->id())
                        <a class="btn btn-sm btn-success flex-fill" target="_blank" rel="noopener"
                           href="https://wa.me/{{ $wa }}?text={{ rawurlencode($p->type === 'birthday' ? 'Happy birthday, '.($p->user->first_name ?: $p->user->name).'! 🎂' : 'Happy work anniversary, '.($p->user->first_name ?: $p->user->name).'! 🎉') }}">
                            <i class="bx bxl-whatsapp"></i> Send wishes
                        </a>
                    @endif
                    @php $shareText = $p->type === 'birthday' ? 'Happy birthday, '.$p->user->name.'! 🎂' : 'Happy work anniversary, '.$p->user->name.'! 🏆'; @endphp
                    <button type="button" class="btn btn-sm btn-outline-success flex-fill" title="Share the picture on WhatsApp" onclick="celShare(this.closest('.col-12').querySelector('.cel-card'), this, {{ json_encode($shareText) }})">
                        <i class="bx bx-share-alt"></i> Share
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" title="Download" onclick="celDownload(this.closest('.col-12').querySelector('.cel-card'), this)">
                        <i class="bx bx-download"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>
@include('hr.celebrations._scripts')
@endif

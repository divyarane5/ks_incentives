@extends('layouts.app')

@section('content')
@include('hr.partials.hr-styles')
<div class="container-xxl flex-grow-1 container-p-y kp" style="max-width:1300px">
    <div class="kp-crumb"><a href="{{ route('users.index') }}">KeyPeople</a> <i class="bx bx-chevron-right"></i> <span>Letters</span></div>
    <div class="hr-head">
        <div><h4>Letters</h4><div class="sub">Offer, appointment, confirmation, increment, relieving and experience letters.</div></div>
        <a href="{{ route('letters.create', request()->only('user_id')) }}" class="btn btn-primary"><i class="bx bx-file-blank"></i> Issue a letter</a>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="kp-panel">
                <div class="kp-panel-h">
                    <h2><i class="bx bx-envelope"></i>Issued letters</h2>
                    <form method="GET" style="min-width:200px">
                        <select name="user_id" class="form-select form-select-sm" onchange="this.form.submit()" data-live-search="true">
                            <option value="">Everyone</option>
                            @foreach($people as $p)<option value="{{ $p->id }}" {{ request('user_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}{{ $p->status !== 'Active' ? ' (left)' : '' }}</option>@endforeach
                        </select>
                    </form>
                </div>
                <div class="kp-panel-b">
                    @forelse($letters as $l)
                        <ul class="kp-list"><li>
                            <div class="ic"><i class="bx bx-file"></i></div>
                            <div class="bd">
                                <div class="t">{{ $l->subject ?: ucfirst($l->type).' letter' }}</div>
                                <div class="m"><a href="{{ route('users.show', $l->user_id) }}">{{ $l->user?->name }}</a> · {{ $l->letter_date->format('d M Y') }} · {{ $l->reference }}</div>
                                <div class="m">Issued by {{ $l->issuer?->name ?? '—' }}</div>
                            </div>
                            <div class="r d-flex gap-1">
                                <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('letters.pdf', $l) }}">PDF</a>
                                <form method="POST" action="{{ route('letters.destroy', $l) }}" onsubmit="return confirm('Remove this letter? The person will no longer see it.')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button></form>
                            </div>
                        </li></ul>
                    @empty
                        <div class="kp-empty">No letters issued yet.</div>
                    @endforelse
                    <div class="mt-2">{{ $letters->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-layout"></i>Templates</h2><a href="{{ route('letters.template.edit') }}" class="btn btn-sm btn-outline-primary">New</a></div>
                <div class="kp-panel-b">
                    <ul class="kp-list">
                        @foreach($templates as $t)
                            <li>
                                <div class="bd">
                                    <div class="t">{{ $t->name }} @unless($t->active)<span class="hr-status cancelled">Not in use</span>@endunless</div>
                                    <div class="m">{{ \App\Models\LetterTemplate::TYPES[$t->type] ?? $t->type }} · {{ $t->subject }}</div>
                                </div>
                                <div class="r"><a href="{{ route('letters.template.edit', $t) }}" class="btn btn-sm btn-outline-secondary">Edit</a></div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
@include('hr.partials.hr-styles')
<div class="container-xxl flex-grow-1 container-p-y kp" style="max-width:1000px">
    <div class="kp-crumb"><a href="{{ route('letters.index') }}">Letters</a> <i class="bx bx-chevron-right"></i> <span>Issue a letter</span></div>
    <h4 class="mb-3">Issue a letter</h4>
    @if($errors->any())<div class="alert alert-danger py-2">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

    <div class="kp-panel">
        <div class="kp-panel-h"><h2><i class="bx bx-user"></i>1. Who and which letter</h2></div>
        <div class="kp-panel-b" style="padding:14px">
            <form method="GET" class="hr-form row g-2 align-items-end">
                <div class="col-12 col-md-6">
                    <label class="form-label">Person</label>
                    <select name="user_id" class="form-select" data-live-search="true" required>
                        <option value="">Choose…</option>
                        @foreach($people as $p)<option value="{{ $p->id }}" {{ $person?->id === $p->id ? 'selected' : '' }}>{{ $p->name }}{{ $p->employee_code ? ' ('.$p->employee_code.')' : '' }}{{ $p->status !== 'Active' ? ' – left' : '' }}</option>@endforeach
                    </select>
                </div>
                <div class="col-8 col-md-4">
                    <label class="form-label">Letter</label>
                    <select name="template_id" class="form-select raw-select" required>
                        <option value="">Choose…</option>
                        @foreach($templates as $t)<option value="{{ $t->id }}" {{ $template?->id === $t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-4 col-md-2"><button class="btn btn-primary w-100" style="min-height:44px">Fill in</button></div>
            </form>
        </div>
    </div>

    @if($filled)
        <form method="POST" action="{{ route('letters.store') }}" class="hr-form">
            @csrf
            <input type="hidden" name="user_id" value="{{ $person->id }}">
            <input type="hidden" name="letter_template_id" value="{{ $template->id }}">
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-edit"></i>2. Check and change if needed</h2></div>
                <div class="kp-panel-b row g-3" style="padding:14px">
                    @if($blanks)
                        <div class="col-12"><div class="alert alert-warning py-2 mb-0">Empty on {{ $person->first_name ?: $person->name }}'s profile: <b>{{ implode(', ', $blanks) }}</b>. Fill them below, or <a href="{{ route('users.edit', $person->id) }}">update the profile</a> first.</div></div>
                    @endif
                    <div class="col-6 col-md-4"><label class="form-label">Letter date</label><input type="date" name="letter_date" class="form-control" value="{{ old('letter_date', today()->toDateString()) }}" required></div>
                    <div class="col-6 col-md-8"><label class="form-label">Reference (optional)</label><input name="reference" class="form-control" value="{{ old('reference') }}" placeholder="Made for you if left empty"></div>
                    <div class="col-12"><label class="form-label">Subject</label><input name="subject" class="form-control" value="{{ old('subject', $filled['subject']) }}"></div>
                    <div class="col-12"><label class="form-label">Letter text</label><textarea name="body" class="form-control" rows="16" style="line-height:1.5" required>{{ old('body', $filled['body']) }}</textarea></div>
                </div>
            </div>
            <div class="kp-savebar">
                <a href="{{ route('letters.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button class="btn btn-primary"><i class="bx bx-check"></i> Issue letter</button>
            </div>
        </form>
    @endif
</div>
@endsection

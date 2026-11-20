@extends('crm.capture.layout')
@section('content')
<h1 class="mb-1 text-xl font-semibold">{{ $form->heading ?: 'Enquire now' }}</h1>
<p class="mb-4 text-sm text-slate-500">Leave your number and we'll call you back{{ $form->project ? ' about '.$form->project->name : '' }}.</p>
@if ($errors->any())
    <div class="mb-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ route('crm.capture.submit', $form->token) }}" class="space-y-3">
    @csrf
    <div>
        <label class="label" for="name">Name</label>
        <input id="name" name="name" class="input" autocomplete="name" value="{{ old('name') }}" required>
    </div>
    <div>
        <label class="label" for="phone">Mobile number</label>
        <input id="phone" name="phone" type="tel" inputmode="tel" class="input" autocomplete="tel" value="{{ old('phone') }}" required>
    </div>
    <div>
        <label class="label" for="email">Email (optional)</label>
        <input id="email" name="email" type="email" class="input" autocomplete="email" value="{{ old('email') }}">
    </div>
    <div>
        <label class="label" for="message">Looking for (optional)</label>
        <textarea id="message" name="message" rows="3" class="input" placeholder="e.g. 2 BHK, budget, location">{{ old('message') }}</textarea>
    </div>
    <div class="hidden" aria-hidden="true"><input name="company_website" tabindex="-1" autocomplete="off"></div>
    <input type="hidden" name="utm_source" value="{{ request('utm_source') }}">
    <input type="hidden" name="utm_campaign" value="{{ request('utm_campaign') }}">
    <button class="btn w-full">Send</button>
    <p class="text-center text-xs text-slate-400">We'll only use your details to contact you about this enquiry.</p>
</form>
@endsection

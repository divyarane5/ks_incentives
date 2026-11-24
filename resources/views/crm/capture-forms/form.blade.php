@extends('crm.layouts.app', ['title' => $form->exists ? $form->name : 'New website form', 'back' => route('crm.capture-forms.index')])
@section('content')
<form method="POST" action="{{ $form->exists ? route('crm.capture-forms.update', $form) : route('crm.capture-forms.store') }}" class="card mb-4 space-y-3">
    @csrf
    @if($form->exists) @method('PUT') @endif
    <div>
        <label class="label" for="name">Name (for you)</label>
        <input id="name" name="name" class="input" required value="{{ old('name', $form->name) }}" placeholder="Main website">
    </div>
    <div>
        <label class="label" for="heading">Heading on the form</label>
        <input id="heading" name="heading" class="input" value="{{ old('heading', $form->heading) }}" placeholder="Enquire now">
    </div>
    <div class="grid gap-3 sm:grid-cols-2">
        <div>
            <label class="label" for="source_id">Lead source</label>
            <select id="source_id" name="source_id" class="input" required>
                @foreach($sources as $s)<option value="{{ $s->id }}" @selected(old('source_id', $form->source_id) == $s->id)>{{ $s->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="label" for="project_id">Project (optional)</label>
            <select id="project_id" name="project_id" class="input">
                <option value="">Any / not specific</option>
                @foreach($projects as $p)<option value="{{ $p->id }}" @selected(old('project_id', $form->project_id) == $p->id)>{{ $p->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="label" for="owner_id">Give new leads to</label>
            <select id="owner_id" name="owner_id" class="input">
                <option value="">Leave unassigned</option>
                @foreach($owners as $o)<option value="{{ $o->id }}" @selected(old('owner_id', $form->owner_id) == $o->id)>{{ $o->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="label" for="thank_you_url">Thank-you page (optional)</label>
            <input id="thank_you_url" name="thank_you_url" type="url" class="input" value="{{ old('thank_you_url', $form->thank_you_url) }}" placeholder="https://yoursite.com/thank-you">
        </div>
    </div>
    <label class="flex items-center gap-3 text-sm">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-slate-300" @checked(old('is_active', $form->is_active))>
        <span>Form is on (untick to stop taking enquiries)</span>
    </label>
    <button class="btn w-full sm:w-auto">{{ $form->exists ? 'Save' : 'Create form' }}</button>
</form>

@if($form->exists)
    <div class="card mb-3 space-y-2">
        <h2 class="font-semibold">1. Ready-made form page</h2>
        <p class="text-sm text-slate-500">Share this link in ads, WhatsApp or social media, or put it behind an "Enquire now" button.</p>
        <div class="flex gap-2">
            <input class="input font-mono text-sm" readonly value="{{ $form->pageUrl() }}" onclick="this.select()">
            <a href="{{ $form->pageUrl() }}" target="_blank" class="btn-light shrink-0">Open</a>
        </div>
    </div>
    <div class="card mb-3 space-y-2">
        <h2 class="font-semibold">2. Show it inside your website</h2>
        <p class="text-sm text-slate-500">Give this code to whoever manages your website. It shows the form on any page.</p>
        <textarea class="input font-mono text-xs" rows="3" readonly onclick="this.select()"><iframe src="{{ $form->pageUrl() }}" style="width:100%;max-width:480px;height:560px;border:0" title="Enquiry form"></iframe></textarea>
    </div>
    <div class="card mb-3 space-y-2">
        <h2 class="font-semibold">3. Connect your website's own form</h2>
        <p class="text-sm text-slate-500">For your web developer: send the existing form to this address (POST, normal form or JSON).</p>
        <input class="input font-mono text-sm" readonly value="{{ $form->postUrl() }}" onclick="this.select()">
        <p class="text-xs text-slate-500">Fields: <code>name</code>, <code>phone</code> (required), <code>email</code>, <code>message</code>, <code>project</code> (exact project name), <code>sub_source</code> (e.g. Microsite), <code>utm_source</code>, <code>utm_campaign</code>, <code>page</code>. Add a hidden empty field <code>company_website</code> to block spam bots.</p>
    </div>
    <p class="text-xs text-slate-500">Keep these links private to your website. Anyone with the link can send enquiries. If one leaks, turn this form off and create a new one.</p>
@endif
@endsection

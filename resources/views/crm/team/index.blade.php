@extends('crm.layouts.app', ['title' => 'Team & settings'])
@section('content')
<div class="mb-3 flex gap-2">
    <form method="GET" class="flex flex-1 gap-2">
        <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search name">
        <button class="btn-light shrink-0">Search</button>
    </form>
    <a href="{{ route('users.index') }}" class="btn shrink-0">Edit people</a>
</div>
<div class="mb-3 flex flex-wrap gap-x-4 gap-y-1 text-sm">
    <a href="{{ route('crm.capture-forms.index') }}" class="font-medium text-brand-700">Website enquiry forms</a>
    <a href="{{ route('crm.sources.index') }}" class="font-medium text-brand-700">Sources and sub sources</a>
    <a href="{{ route('crm.assignment-rules.index') }}" class="font-medium text-brand-700">Auto-assignment</a>
    <a href="{{ route('crm.message-templates.index') }}" class="font-medium text-brand-700">WhatsApp messages</a>
    <a href="{{ route('crm.imports.index') }}" class="font-medium text-brand-700">Excel upload</a>
</div>
<p class="mb-3 text-sm text-slate-500">People, their role (FOS, TL, Sr. TL, CH) and who they report to are set in the back office under Users. FOS = sales, TL = manager, Sr. TL and CH = general manager.</p>
@if($noManager)
    <div class="mb-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">{{ $noManager }} active sales team member(s) have no reporting manager set. Their leads won't show up for any manager.</div>
@endif
<div class="space-y-2">
    @foreach($users as $u)
        <a href="{{ route('users.edit', $u) }}" class="card flex items-center justify-between p-3">
            <div>
                <div class="font-semibold">{{ $u->name }}</div>
                <div class="text-sm text-slate-500">{{ $u->getRoleNames()->first() }} · {{ $roles[$u->crmRole()] ?? '' }}@if($u->manager) · reports to {{ $u->manager->name }}@endif</div>
            </div>
            <div class="text-right text-sm">
                @if($u->active_leads_count === 0 && $u->crmRole() === 'sales')<span class="chip bg-amber-100 text-amber-800">No leads</span>
                @else<span class="text-slate-600">{{ $u->active_leads_count }} active</span>@endif
            </div>
        </a>
    @endforeach
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection

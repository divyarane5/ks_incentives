@extends('crm.layouts.app', ['title' => 'Today'])
@section('content')
<p class="mb-3 text-sm text-slate-500">Hi {{ strtok(auth()->user()->name, ' ') }}, here's your day.</p>

<div class="mb-5 grid grid-cols-3 gap-3">
    <a href="{{ route('crm.leads.index', ['tab' => 'overdue']) }}" class="card p-3 {{ $counts['overdue'] ? 'border-red-200 bg-red-50' : '' }}">
        <div class="text-2xl font-bold {{ $counts['overdue'] ? 'text-red-600' : '' }}">{{ $counts['overdue'] }}</div>
        <div class="text-xs text-slate-600">Overdue</div>
    </a>
    <a href="{{ route('crm.leads.index', ['tab' => 'today']) }}" class="card p-3">
        <div class="text-2xl font-bold">{{ $counts['today'] }}</div>
        <div class="text-xs text-slate-600">Due today</div>
    </a>
    <a href="{{ route('crm.leads.index', ['tab' => 'mine']) }}" class="card p-3">
        <div class="text-2xl font-bold">{{ $counts['new'] }}</div>
        <div class="text-xs text-slate-600">Not yet called</div>
    </a>
</div>

@if($appointments->isNotEmpty())
    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-slate-500">Meetings and site visits (next 3 days)</h2>
    <div class="mb-5 space-y-2">
        @foreach($appointments as $a)
            <a href="{{ route('crm.leads.show', $a->lead) }}" class="card flex items-center justify-between p-3">
                <div>
                    <div class="font-semibold">{{ $a->lead->name }}</div>
                    <div class="text-sm text-slate-500">{{ $a->type === 'site_visit' ? 'Site visit' : 'Meeting' }} · {{ $a->scheduled_at->format('D d M, h:i A') }}</div>
                </div>
                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </a>
        @endforeach
    </div>
@endif

<h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-slate-500">Calls to make</h2>
<div class="mb-5 space-y-2">
    @forelse($todayLeads as $lead)
        @include('crm.partials.lead-card')
    @empty
        <div class="card text-center text-sm text-slate-500">Nothing due. Nice work.</div>
    @endforelse
</div>

@if($newLeads->isNotEmpty())
    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-slate-500">New leads, not yet called</h2>
    <div class="mb-5 space-y-2">
        @foreach($newLeads as $lead)
            @include('crm.partials.lead-card')
        @endforeach
    </div>
@endif

@if($teamOverdue->isNotEmpty())
    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-slate-500">Team members with overdue follow-ups</h2>
    <div class="card divide-y divide-slate-100 p-0">
        @foreach($teamOverdue as $row)
            <a href="{{ route('crm.leads.index', ['tab' => 'overdue', 'owner' => $row->owner_id]) }}" class="flex items-center justify-between px-4 py-3">
                <span>{{ $row->owner?->name }}</span>
                <span class="chip bg-red-100 text-red-700">{{ $row->total }} overdue</span>
            </a>
        @endforeach
    </div>
@endif
@endsection

@extends('crm.layouts.app', ['title' => $project->name, 'back' => route('crm.projects.index')])
@section('content')
<div class="card mb-3 text-sm">
    <div class="mb-2 flex items-start justify-between">
        <div>
            <div class="text-lg font-semibold">{{ $project->name }}</div>
            <div class="text-slate-500">{{ ucfirst($project->category) }}@if($project->launch_status) · {{ ucfirst(str_replace('_', ' ', $project->launch_status)) }}@endif</div>
        </div>
        @if(auth()->user()->isAdmin())<a href="{{ route('crm.projects.edit', $project) }}" class="font-medium text-brand-700">Edit</a>@endif
    </div>
    <dl class="grid grid-cols-2 gap-x-3 gap-y-2">
        <dt class="text-slate-500">Developer</dt><dd>{{ $project->developer?->name ?? '—' }}</dd>
        <dt class="text-slate-500">Location</dt><dd>{{ collect([$project->location, $project->city])->filter()->join(', ') ?: '—' }}</dd>
        <dt class="text-slate-500">Price</dt><dd>{{ \App\Support\Money::short($project->min_price) }} – {{ \App\Support\Money::short($project->max_price) }}</dd>
        <dt class="text-slate-500">Possession</dt><dd>{{ $project->possession_date?->format('M Y') ?? '—' }}</dd>
        <dt class="text-slate-500">Leads</dt><dd><a class="text-brand-700" href="{{ route('crm.leads.index', ['tab' => 'team', 'project' => $project->id]) }}">{{ $project->leads_count }}</a></dd>
    </dl>
    @if($project->description)<p class="mt-3 whitespace-pre-line text-slate-700">{{ $project->description }}</p>@endif
</div>

<div class="mb-2 flex items-center justify-between">
    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Units / properties</h2>
    @if(auth()->user()->isAdmin())<a href="{{ route('crm.properties.create', ['project' => $project->id]) }}" class="text-sm font-medium text-brand-700">+ Add</a>@endif
</div>
<div class="space-y-2">
    @forelse($project->properties as $prop)
        @include('crm.properties._card', ['property' => $prop])
    @empty
        <div class="card text-center text-sm text-slate-500">No properties added.</div>
    @endforelse
</div>
@endsection

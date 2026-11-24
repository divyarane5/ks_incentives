@extends('crm.layouts.app', ['title' => 'Auto-assignment', 'back' => route('crm.team.index')])
@section('content')
<div class="mb-3 flex items-start justify-between gap-3">
    <p class="text-sm text-slate-500">New leads with no owner (website forms, uploads) go to the next person in turn. The first rule that matches the lead is used, top to bottom. Leads that match no rule stay unassigned.</p>
    <a href="{{ route('crm.assignment-rules.create') }}" class="btn shrink-0">+ Rule</a>
</div>
<div class="space-y-2">
    @forelse($rules as $r)
        <a href="{{ route('crm.assignment-rules.edit', $r) }}" class="card block p-3 {{ $r->is_active ? '' : 'opacity-60' }}">
            <div class="flex items-center justify-between gap-2">
                <div class="font-semibold">{{ $r->name }}</div>
                @if($r->is_active)<span class="text-sm text-slate-500">{{ number_format($r->assigned_count) }} assigned</span>@else<span class="chip bg-slate-200 text-slate-600">Off</span>@endif
            </div>
            <div class="mt-1 text-sm text-slate-500">
                {{ $r->source?->name ?? 'Any source' }} · {{ $r->project?->name ?? 'Any project' }}
            </div>
            <div class="mt-1 text-sm">{{ collect($r->user_ids)->map(fn ($id) => $names[$id] ?? null)->filter()->join(', ') }}</div>
        </a>
    @empty
        <div class="card text-center text-sm text-slate-500">No rules yet. Without rules, new website and upload leads stay unassigned.</div>
    @endforelse
</div>
@endsection

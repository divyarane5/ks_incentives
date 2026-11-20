@extends('crm.layouts.app', ['title' => 'Website forms', 'back' => route('crm.team.index')])
@section('content')
<div class="mb-3 flex items-center justify-between gap-2">
    <p class="text-sm text-slate-500">Enquiries from these forms land in KeyLeads automatically, with the duplicate check.</p>
    <a href="{{ route('crm.capture-forms.create') }}" class="btn shrink-0">+ Form</a>
</div>
<div class="space-y-2">
    @forelse($forms as $f)
        <a href="{{ route('crm.capture-forms.edit', $f) }}" class="card flex items-center justify-between gap-3 p-3 {{ $f->is_active ? '' : 'opacity-60' }}">
            <div class="min-w-0">
                <div class="font-semibold">{{ $f->name }}</div>
                <div class="truncate text-sm text-slate-500">{{ $f->source?->name }}@if($f->project) · {{ $f->project->name }}@endif · {{ $f->owner?->name ?? 'Unassigned' }}</div>
            </div>
            <div class="shrink-0 text-right text-sm">
                @unless($f->is_active)<span class="chip bg-slate-200 text-slate-600">Off</span>@endunless
                <div class="font-semibold">{{ number_format($f->leads_count) }}</div>
                <div class="text-xs text-slate-500">{{ $f->last_used_at ? 'last '.$f->last_used_at->diffForHumans() : 'no leads yet' }}</div>
            </div>
        </a>
    @empty
        <div class="card text-center text-sm text-slate-500">No forms yet. Create one for your website.</div>
    @endforelse
</div>
@endsection

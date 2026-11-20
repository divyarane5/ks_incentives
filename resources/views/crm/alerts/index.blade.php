@extends('crm.layouts.app', ['title' => 'Alerts'])
@section('content')
<div class="mb-4 grid grid-cols-3 gap-3">
    <a href="{{ route('crm.leads.index', ['tab' => 'overdue', 'owner' => auth()->id()]) }}" class="card p-3 {{ $mine['overdue'] ? 'border-red-200 bg-red-50' : '' }}">
        <div class="text-2xl font-bold {{ $mine['overdue'] ? 'text-red-600' : '' }}">{{ $mine['overdue'] }}</div>
        <div class="text-xs text-slate-600">My overdue</div>
    </a>
    <a href="{{ route('crm.leads.index', ['tab' => 'today']) }}" class="card p-3">
        <div class="text-2xl font-bold">{{ $mine['today'] }}</div>
        <div class="text-xs text-slate-600">Still due today</div>
    </a>
    <a href="{{ route('crm.leads.index', ['tab' => 'mine']) }}" class="card p-3">
        <div class="text-2xl font-bold">{{ $mine['untouched'] }}</div>
        <div class="text-xs text-slate-600">Not yet called</div>
    </a>
</div>

@if($team)
    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-slate-500">Team overdue</h2>
    <div class="card mb-5 divide-y divide-slate-100 p-0">
        @foreach($team as $row)
            <a href="{{ route('crm.leads.index', ['tab' => 'overdue', 'owner' => $row['user']->id]) }}" class="flex items-center justify-between px-4 py-2.5 text-sm">
                <span>{{ $row['user']->name }}</span>
                <span class="font-semibold text-red-600">{{ $row['overdue'] }}</span>
            </a>
        @endforeach
    </div>
@endif

<h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-slate-500">Recent</h2>
<div class="space-y-2">
    @forelse($alerts as $n)
        <a href="{{ $n->data['url'] ?? '#' }}" class="card flex gap-3 p-3 {{ $n->read_at ? '' : 'border-gold-400' }}">
            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $n->read_at ? 'bg-slate-200' : 'bg-gold-500' }}"></span>
            <div class="min-w-0">
                <div class="font-semibold">{{ $n->data['title'] ?? '' }}</div>
                <div class="text-sm text-slate-600">{{ $n->data['body'] ?? '' }}</div>
                <div class="mt-0.5 text-xs text-slate-400">{{ $n->created_at->diffForHumans() }}</div>
            </div>
        </a>
    @empty
        <div class="card text-center text-sm text-slate-500">No alerts yet. New leads given to you and your morning summary show up here.</div>
    @endforelse
</div>
<div class="mt-4">{{ $alerts->links() }}</div>
@endsection

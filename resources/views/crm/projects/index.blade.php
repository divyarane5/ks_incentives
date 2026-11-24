@extends('crm.layouts.app', ['title' => 'Projects'])
@section('content')
<div class="mb-3 flex gap-2">
    <form method="GET" class="flex flex-1 gap-2">
        <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search project or location">
        <button class="btn-light shrink-0">Search</button>
    </form>

</div>
<div class="mb-3 flex gap-2 text-sm">
    <a href="{{ route('crm.properties.index') }}" class="font-medium text-brand-700">View properties →</a>
</div>
<div class="space-y-2">
    @forelse($projects as $p)
        <a href="{{ route('crm.projects.show', $p) }}" class="card block p-3">
            <div class="flex items-center justify-between gap-2">
                <span class="font-semibold">{{ $p->name }}</span>
                @unless($p->is_active)<span class="chip bg-slate-200 text-slate-600">Inactive</span>@endunless
            </div>
            <div class="text-sm text-slate-500">
                {{ ucfirst($p->category) }}@if($p->developer) · {{ $p->developer->name }}@endif @if($p->location) · {{ $p->location }}@endif
            </div>
            <div class="mt-1 text-sm text-slate-600">
                @if($p->min_price || $p->max_price){{ \App\Support\Money::short($p->min_price) }} – {{ \App\Support\Money::short($p->max_price) }} · @endif{{ $p->leads_count }} leads
            </div>
        </a>
    @empty
        <div class="card text-center text-sm text-slate-500">No projects yet.</div>
    @endforelse
</div>
<div class="mt-4">{{ $projects->onEachSide(1)->links() }}</div>
@endsection

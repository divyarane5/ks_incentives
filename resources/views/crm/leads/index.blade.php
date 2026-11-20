@extends('crm.layouts.app', ['title' => 'Leads'])
@section('content')
{{-- Tabs scroll sideways on phones --}}
<div class="-mx-4 mb-3 flex gap-2 overflow-x-auto px-4 pb-1">
    @foreach($tabs as $key => $label)
        <a href="{{ route('crm.leads.index', ['tab' => $key]) }}"
           class="shrink-0 rounded-full px-4 py-1.5 text-sm font-medium {{ $tab === $key ? 'bg-brand-700 text-white' : 'bg-white text-slate-700 border border-slate-200' }}">{{ $label }}</a>
    @endforeach
</div>

<form method="GET" class="mb-3">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <div class="flex gap-2">
        <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search name or phone">
        <button class="btn-light shrink-0">Search</button>
    </div>
    <details class="mt-2" @if(request()->hasAny(['stage','source','sub_source','project','owner','temperature','from','to'])) open @endif>
        <summary class="cursor-pointer text-sm font-medium text-brand-700">Filters</summary>
        <div class="mt-2 grid grid-cols-2 gap-2 md:grid-cols-4">
            <select name="stage" class="input"><option value="">Any stage</option>
                @foreach($stages as $s)<option value="{{ $s->id }}" @selected(request('stage') == $s->id)>{{ $s->name }}</option>@endforeach
            </select>
            <select name="source" class="input"><option value="">Any source</option>
                @foreach($sources as $s)<option value="{{ $s->id }}" @selected(request('source') == $s->id)>{{ $s->name }}</option>@endforeach
            </select>
            @if($sources->contains(fn ($s) => $s->subSources->isNotEmpty()))
                <select name="sub_source" class="input"><option value="">Any sub source</option>
                    @foreach($sources as $s)
                        @if($s->subSources->isNotEmpty())
                            <optgroup label="{{ $s->name }}">
                                @foreach($s->subSources as $sub)<option value="{{ $sub->name }}" @selected(request('sub_source') === $sub->name)>{{ $sub->name }}</option>@endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>
            @endif
            <select name="project" class="input"><option value="">Any project</option>
                @foreach($projects as $p)<option value="{{ $p->id }}" @selected(request('project') == $p->id)>{{ $p->name }}</option>@endforeach
            </select>
            <select name="temperature" class="input"><option value="">Hot / warm / cold</option>
                @foreach(['hot','warm','cold'] as $t)<option value="{{ $t }}" @selected(request('temperature') === $t)>{{ ucfirst($t) }}</option>@endforeach
            </select>
            @if($owners->count() > 1)
                <select name="owner" class="input"><option value="">Any owner</option>
                    @foreach($owners as $o)<option value="{{ $o->id }}" @selected(request('owner') == $o->id)>{{ $o->name }}</option>@endforeach
                </select>
            @endif
            <input type="date" name="from" value="{{ request('from') }}" class="input" aria-label="Created from">
            <input type="date" name="to" value="{{ request('to') }}" class="input" aria-label="Created to">
            <button class="btn">Apply</button>
        </div>
    </details>
</form>

<div class="mb-2 flex items-center justify-between text-sm text-slate-500">
    <span>{{ number_format($leads->total()) }} leads</span>
    @if($canAssign)
        <a href="{{ route('crm.imports.index') }}" class="font-medium text-brand-700">Upload from Excel</a>
    @endif
</div>

@if($canAssign && $leads->isNotEmpty())
    <form id="bulk-assign" method="POST" action="{{ route('crm.leads.bulk-assign') }}" class="card mb-3 flex flex-wrap items-center gap-2 p-3">
        @csrf
        <span class="text-sm text-slate-600">Assign ticked leads to</span>
        <select name="owner_id" class="input w-auto flex-1" required>
            <option value="">Choose person</option>
            @foreach($owners as $o)<option value="{{ $o->id }}">{{ $o->name }}</option>@endforeach
        </select>
        <button class="btn">Assign</button>
    </form>
@endif

<div class="space-y-2">
    @forelse($leads as $lead)
        @include('crm.partials.lead-card', ['showOwner' => true] + ($canAssign ? ['selectable' => true] : []))
    @empty
        <div class="card text-center text-sm text-slate-500">No leads here.</div>
    @endforelse
</div>

<div class="mt-4">{{ $leads->onEachSide(1)->links() }}</div>
@endsection

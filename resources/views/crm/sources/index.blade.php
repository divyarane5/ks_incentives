@extends('crm.layouts.app', ['title' => 'Sources', 'back' => route('crm.team.index')])
@section('content')
<p class="mb-3 text-sm text-slate-500">Each source can have sub sources, for example Website: Microsite, Portal, Landing page. Staff pick them from a list, so the spelling stays the same and reports add up. New names sent by uploads, website forms or Facebook are added here on their own. Hidden ones stay on old leads but can't be picked.</p>

<div class="space-y-2">
    @foreach($sources as $source)
        <details class="card p-3 {{ $source->is_active ? '' : 'opacity-60' }}" @if(session('open') == $source->id || $errors->any() && old('source') == $source->id) open @endif>
            <summary class="flex cursor-pointer items-center justify-between gap-2">
                <span class="font-semibold">{{ $source->name }}</span>
                <span class="text-sm text-slate-500">{{ $source->subSources->count() }} sub {{ Str::plural('source', $source->subSources->count()) }}@unless($source->is_active) · hidden @endunless</span>
            </summary>

            <div class="mt-3 space-y-2">
                @foreach($source->subSources as $sub)
                    <form method="POST" action="{{ route('crm.sub-sources.update', $sub) }}" class="flex items-center gap-2">
                        @csrf @method('PUT')
                        <input type="hidden" name="source" value="{{ $source->id }}">
                        <input class="input" name="name" value="{{ $sub->name }}" aria-label="Sub source name" required maxlength="100">
                        <span class="w-14 shrink-0 text-right text-xs text-slate-500">@php($n = $counts[$source->id.'|'.mb_strtolower($sub->name)] ?? 0){{ number_format($n) }} {{ Str::plural('lead', $n) }}</span>
                        <label class="flex shrink-0 items-center gap-1 text-sm"><input type="checkbox" name="is_active" value="1" @checked($sub->is_active)> On</label>
                        <button class="btn-light shrink-0">Save</button>
                    </form>
                @endforeach

                <form method="POST" action="{{ route('crm.sub-sources.store', $source) }}" class="flex items-center gap-2 border-t border-slate-100 pt-2">
                    @csrf
                    <input type="hidden" name="source" value="{{ $source->id }}">
                    <input class="input" name="name" placeholder="New sub source, e.g. Microsite" aria-label="New sub source for {{ $source->name }}" required maxlength="100">
                    <button class="btn shrink-0">Add</button>
                </form>

                <form method="POST" action="{{ route('crm.sources.update', $source) }}" class="flex items-center gap-2 border-t border-slate-100 pt-2">
                    @csrf @method('PUT')
                    <input type="hidden" name="source" value="{{ $source->id }}">
                    <input class="input" name="name" value="{{ $source->name }}" aria-label="Source name" required maxlength="100">
                    <label class="flex shrink-0 items-center gap-1 text-sm"><input type="checkbox" name="is_active" value="1" @checked($source->is_active)> On</label>
                    <button class="btn-light shrink-0">Save source</button>
                </form>
            </div>
        </details>
    @endforeach
</div>

<form method="POST" action="{{ route('crm.sources.store') }}" class="card mt-4 flex items-center gap-2 p-3">
    @csrf
    <input class="input" name="name" placeholder="New source, e.g. NoBroker" aria-label="New source" required maxlength="100">
    <button class="btn shrink-0">+ Source</button>
</form>
@endsection

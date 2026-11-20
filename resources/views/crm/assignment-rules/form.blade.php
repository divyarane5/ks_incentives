@extends('crm.layouts.app', ['title' => $rule->exists ? $rule->name : 'New rule', 'back' => route('crm.assignment-rules.index')])
@section('content')
<form method="POST" action="{{ $rule->exists ? route('crm.assignment-rules.update', $rule) : route('crm.assignment-rules.store') }}" class="card space-y-3">
    @csrf
    @if($rule->exists) @method('PUT') @endif
    <div>
        <label class="label" for="name">Rule name</label>
        <input id="name" name="name" class="input" required value="{{ old('name', $rule->name) }}" placeholder="e.g. Website leads">
    </div>
    <div class="grid gap-3 sm:grid-cols-2">
        <div>
            <label class="label" for="source_id">When the source is</label>
            <select id="source_id" name="source_id" class="input">
                <option value="">Any source</option>
                @foreach($sources as $s)<option value="{{ $s->id }}" @selected(old('source_id', $rule->source_id) == $s->id)>{{ $s->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="label" for="project_id">and the project is</label>
            <select id="project_id" name="project_id" class="input">
                <option value="">Any project</option>
                @foreach($projects as $p)<option value="{{ $p->id }}" @selected(old('project_id', $rule->project_id) == $p->id)>{{ $p->name }}</option>@endforeach
            </select>
        </div>
    </div>
    <div>
        <div class="label">Share the leads, in turn, between</div>
        @php($ticked = array_map('intval', old('user_ids', $rule->user_ids ?? [])))
        <div class="grid gap-1 sm:grid-cols-2">
            @foreach($people as $u)
                <label class="flex items-center gap-3 rounded-lg px-2 py-2 text-sm hover:bg-slate-50">
                    <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" class="h-5 w-5 rounded border-slate-300" @checked(in_array($u->id, $ticked, true))>
                    <span>{{ $u->name }} <span class="text-slate-400">{{ $u->role === 'manager' ? '(manager)' : '' }}</span></span>
                </label>
            @endforeach
        </div>
        <p class="mt-1 text-xs text-slate-500">People marked inactive are skipped automatically.</p>
    </div>
    <div class="grid gap-3 sm:grid-cols-2">
        <div>
            <label class="label" for="sort_order">Order (lower is checked first)</label>
            <input id="sort_order" name="sort_order" type="number" min="0" class="input" value="{{ old('sort_order', $rule->sort_order ?? 0) }}">
        </div>
        <label class="flex items-center gap-3 self-end pb-2 text-sm">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-slate-300" @checked(old('is_active', $rule->is_active))>
            <span>Rule is on</span>
        </label>
    </div>
    <button class="btn w-full sm:w-auto">{{ $rule->exists ? 'Save' : 'Add rule' }}</button>
</form>
@if($rule->exists)
    <form method="POST" action="{{ route('crm.assignment-rules.destroy', $rule) }}" class="mt-3" onsubmit="return confirm('Delete this rule?')">
        @csrf @method('DELETE')
        <button class="text-sm font-medium text-red-600">Delete rule</button>
    </form>
@endif
@endsection

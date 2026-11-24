@extends('crm.layouts.app', ['title' => $lead->name, 'back' => route('crm.leads.index')])
@section('content')
@php($e = $lead->enquiry)
@php($tel = '+'.$lead->country_code.$lead->phone)

<div class="card mb-3">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <div class="text-lg font-semibold">{{ $lead->name }}</div>
            <div class="text-slate-600">{{ $tel }}</div>
            @if($lead->email)<div class="truncate text-sm text-slate-500">{{ $lead->email }}</div>@endif
            <div class="mt-2 flex flex-wrap gap-1.5">
                @include('crm.partials.stage-chip')
                @if($lead->temperature)<span class="chip bg-slate-100 text-slate-700">{{ ucfirst($lead->temperature) }}</span>@endif
                @if($lead->is_archived)<span class="chip bg-slate-200 text-slate-700">Archived</span>@endif
                @if($lead->re_enquiry_count)<span class="chip bg-violet-100 text-violet-700">Re-enquired ×{{ $lead->re_enquiry_count }}</span>@endif
            </div>
        </div>
        @can('update', $lead)
            <a href="{{ route('crm.leads.edit', $lead) }}" class="text-sm font-medium text-brand-700">Edit</a>
        @endcan
    </div>

    @if($lead->next_follow_up_at)
        <div class="mt-3 rounded-lg px-3 py-2 text-sm {{ $lead->next_follow_up_at->isPast() ? 'bg-red-50 text-red-700' : 'bg-brand-50 text-brand-800' }}">
            {{ $lead->next_follow_up_at->isPast() ? 'Overdue' : 'Next' }}: {{ str_replace('_', ' ', $lead->next_follow_up_type ?? 'follow-up') }} on {{ $lead->next_follow_up_at->format('D d M, h:i A') }}
        </div>
    @endif

    <div class="mt-3 grid grid-cols-2 gap-2">
        <a href="tel:{{ $tel }}" class="btn bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800" data-call-button>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.3a1 1 0 01.9.7l1.5 4.5a1 1 0 01-.5 1.2l-2.3 1.1a11 11 0 005.5 5.5l1.1-2.3a1 1 0 011.2-.5l4.5 1.5a1 1 0 01.7 1V19a2 2 0 01-2 2h-1C9.7 21 3 14.3 3 6V5z"/></svg>Call</a>
        <button type="button" class="btn-light" onclick="document.getElementById('wa-sheet').classList.toggle('hidden')">WhatsApp</button>
    </div>
    <div id="wa-sheet" class="mt-2 hidden overflow-hidden rounded-lg border border-slate-200">
        <div class="bg-slate-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Send on WhatsApp</div>
        @foreach($templates as $t)
            <a href="{{ route('crm.leads.whatsapp', [$lead, 'template' => $t->id]) }}" target="_blank" rel="noopener" class="block border-t border-slate-100 px-3 py-2.5 hover:bg-slate-50">
                <div class="text-sm font-medium">{{ $t->name }}</div>
                <div class="line-clamp-2 text-xs text-slate-500">{{ $t->textFor($lead, auth()->user()) }}</div>
            </a>
        @endforeach
        <a href="{{ route('crm.leads.whatsapp', $lead) }}" target="_blank" rel="noopener" class="block border-t border-slate-100 px-3 py-2.5 text-sm font-medium text-brand-700 hover:bg-slate-50">Blank message</a>
    </div>
</div>

{{-- Shown after tapping Call, so the outcome is logged straight away --}}
<form method="POST" action="{{ route('crm.leads.call', $lead) }}" id="call-outcome" class="card mb-3 hidden space-y-3 border-emerald-300">
    @csrf
    <h2 class="font-semibold">How did the call go?</h2>
    <div class="grid grid-cols-2 gap-2">
        @foreach(['answered' => 'Answered', 'not_answered' => 'Not answered', 'busy' => 'Busy', 'not_reachable' => 'Not reachable', 'switched_off' => 'Switched off', 'wrong_number' => 'Wrong number'] as $k => $v)
            <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2.5 text-sm has-[:checked]:border-gold-500 has-[:checked]:bg-brand-50">
                <input type="radio" name="outcome" value="{{ $k }}" required> {{ $v }}
            </label>
        @endforeach
    </div>
    <div class="grid grid-cols-3 gap-2">
        <input class="input col-span-1" type="number" name="duration_minutes" min="0" inputmode="numeric" placeholder="Mins">
        <input class="input col-span-2" name="notes" placeholder="What was discussed?">
    </div>
    <button class="btn w-full">Save call</button>
</form>

@can('update', $lead)
<form method="POST" action="{{ route('crm.leads.stage', $lead) }}" class="card mb-3 space-y-3" id="stage-form">
    @csrf
    <h2 class="font-semibold">Update status</h2>
    <div class="grid grid-cols-2 gap-2">
        <select name="stage_id" class="input" required data-stage-select>
            @foreach($stages as $s)
                <option value="{{ $s->id }}" data-schedule="{{ $s->schedule_type }}" data-closed="{{ $s->outcome !== 'open' ? 1 : 0 }}" data-booked="{{ $s->slug === \App\Services\BookingFromLead::STAGE_SLUG ? 1 : 0 }}"
                        data-children='@json($s->children->map(fn ($c) => ['id' => $c->id, 'name' => $c->name]))'
                        @selected(old('stage_id', $lead->stage_id) == $s->id)>{{ $s->name }}</option>
            @endforeach
        </select>
        <select name="sub_stage_id" class="input" data-substage-select data-current="{{ old('sub_stage_id', $lead->sub_stage_id) }}"></select>
    </div>
    <div data-followup>
        <label class="label" for="follow_up_at"><span data-followup-label>Next follow-up</span></label>
        <input class="input" type="datetime-local" id="follow_up_at" name="follow_up_at" value="{{ old('follow_up_at') }}">
        <div class="mt-2 flex flex-wrap gap-2 text-xs">
            @foreach(['+1 hour' => '1 hour', 'today 18:00' => 'This evening', 'tomorrow 11:00' => 'Tomorrow 11am', '+2 days 11:00' => 'In 2 days', '+7 days 11:00' => 'Next week'] as $when => $label)
                <button type="button" class="rounded-full border border-slate-300 px-3 py-1" data-quick-time="{{ \Illuminate\Support\Carbon::parse($when)->format('Y-m-d\TH:i') }}">{{ $label }}</button>
            @endforeach
        </div>
    </div>
    <div data-project-pick class="hidden">
        <label class="label" for="project_id">Project</label>
        <select class="input" name="project_id" id="project_id">
            <option value="">Choose project</option>
            @foreach($lead->projects->isNotEmpty() ? $lead->projects : $projects as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
        </select>
    </div>
    <textarea name="note" class="input" rows="2" placeholder="Note (optional)">{{ old('note') }}</textarea>
    <button class="btn w-full">Save</button>
</form>
@endcan

@if($lead->appointments->isNotEmpty())
<div class="card mb-3">
    <h2 class="mb-2 font-semibold">Meetings and site visits</h2>
    <div class="divide-y divide-slate-100">
        @foreach($lead->appointments as $a)
            <div class="flex flex-wrap items-center justify-between gap-2 py-2 text-sm">
                <div>
                    <div class="font-medium">{{ $a->type === 'site_visit' ? 'Site visit' : 'Meeting' }} @if($a->mode)({{ str_replace('_', ' ', $a->mode) }})@endif</div>
                    <div class="text-slate-500">{{ $a->scheduled_at->format('D d M, h:i A') }} @if($a->project)· {{ $a->project->name }}@endif</div>
                </div>
                @if($a->status === 'scheduled')
                    <div class="flex gap-1">
                        @foreach(['done' => 'Done', 'not_done' => 'Not done'] as $k => $v)
                            <form method="POST" action="{{ route('crm.appointments.update', $a) }}">@csrf
                                <input type="hidden" name="status" value="{{ $k }}">
                                <button class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium">{{ $v }}</button>
                            </form>
                        @endforeach
                    </div>
                @else
                    <span class="chip {{ $a->status === 'done' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ str_replace('_', ' ', ucfirst($a->status)) }}</span>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="card mb-3 text-sm">
    <h2 class="mb-2 font-semibold">Details</h2>
    <dl class="grid grid-cols-2 gap-x-3 gap-y-2">
        <dt class="text-slate-500">Owner</dt><dd>{{ $lead->owner?->name ?? 'Unassigned' }}</dd>
        @if($lead->secondaryOwner)<dt class="text-slate-500">Second owner</dt><dd>{{ $lead->secondaryOwner->name }}</dd>@endif
        <dt class="text-slate-500">Source</dt><dd>{{ $lead->source?->name }}@if($lead->sub_source) · {{ $lead->sub_source }}@endif</dd>
        <dt class="text-slate-500">Looking to</dt><dd>{{ ucfirst($e?->enquired_for ?? 'buy') }}@if($e?->propertyType) · {{ $e->propertyType->name }}@endif @if($e?->bhk) · {{ rtrim(rtrim($e->bhk, '0'), '.') }} BHK @endif</dd>
        @if($e?->budget_min || $e?->budget_max)
            <dt class="text-slate-500">Budget</dt><dd>{{ \App\Support\Money::short($e->budget_min) }} – {{ \App\Support\Money::short($e->budget_max) }}</dd>
        @endif
        @if($e?->locations)<dt class="text-slate-500">Locations</dt><dd>{{ $e->locations }}</dd>@endif
        @if($lead->projects->isNotEmpty())<dt class="text-slate-500">Projects</dt><dd>{{ $lead->projects->pluck('name')->join(', ') }}</dd>@endif
        @if($lead->booking)<dt class="text-slate-500">Booking</dt><dd>@can('booking-view')<a class="font-medium text-brand-700" href="{{ route('booking.show', $lead->booking) }}">#{{ $lead->booking->id }}</a>@else #{{ $lead->booking->id }}@endcan · {{ ucfirst($lead->booking->booking_confirm ?? 'pending') }}</dd>@endif
        <dt class="text-slate-500">Lead no.</dt><dd>{{ $lead->serial_no }}</dd>
        <dt class="text-slate-500">Added</dt><dd>{{ $lead->created_at->format('d M Y') }}</dd>
    </dl>
</div>

@if($canAssign)
<form method="POST" action="{{ route('crm.leads.assign', $lead) }}" class="card mb-3 space-y-2">
    @csrf
    <h2 class="font-semibold">Assign</h2>
    <div class="grid grid-cols-2 gap-2">
        <select name="owner_id" class="input" required>
            @foreach($owners as $o)<option value="{{ $o->id }}" @selected($lead->owner_id === $o->id)>{{ $o->name }}</option>@endforeach
        </select>
        <select name="secondary_owner_id" class="input">
            <option value="">No second owner</option>
            @foreach($owners as $o)<option value="{{ $o->id }}" @selected($lead->secondary_owner_id === $o->id)>{{ $o->name }}</option>@endforeach
        </select>
    </div>
    <button class="btn-light w-full">Assign</button>
</form>
@endif

<div class="card">
    <h2 class="mb-2 font-semibold">Timeline</h2>
    <form method="POST" action="{{ route('crm.leads.note', $lead) }}" class="mb-3 flex gap-2">
        @csrf
        <input name="note" class="input" placeholder="Add a note" required>
        <button class="btn-light shrink-0">Add</button>
    </form>
    <ol class="space-y-3 text-sm">
        @foreach($lead->activities as $act)
            <li class="border-l-2 border-slate-200 pl-3">
                <div class="font-medium">
                    @switch($act->type)
                        @case('created') Lead added @break
                        @case('stage_changed') {{ $act->from_value }} → {{ $act->to_value }} @break
                        @case('assigned') Assigned: {{ $act->from_value }} → {{ $act->to_value }} @break
                        @case('call') Call: {{ $act->to_value }} @break
                        @case('follow_up_set') Follow-up set for {{ $act->to_value }} @break
                        @case('re_enquiry') Enquired again @break
                        @case('appointment') {{ $act->to_value }} @break
                        @case('note') Note @break
                        @case('whatsapp') WhatsApp: {{ $act->to_value }} @break
                        @default {{ ucfirst(str_replace('_', ' ', $act->type)) }}
                    @endswitch
                </div>
                @if($act->body)<div class="whitespace-pre-line text-slate-700">{{ $act->body }}</div>@endif
                <div class="text-xs text-slate-400">{{ $act->user?->name ?? 'System' }} · {{ $act->created_at->format('d M Y, h:i A') }}</div>
            </li>
        @endforeach
    </ol>
</div>

@can('delete', $lead)
    <form method="POST" action="{{ route('crm.leads.destroy', $lead) }}" class="mt-4 text-center" onsubmit="return confirm('Delete this lead?')">
        @csrf @method('DELETE')
        <button class="text-sm text-red-600">Delete lead</button>
    </form>
@endcan
@endsection

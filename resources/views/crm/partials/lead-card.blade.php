{{-- One lead as a tappable card, used in lists. --}}
@php($overdue = $lead->next_follow_up_at && $lead->next_follow_up_at->isPast() && ! $lead->is_archived)
<div class="card flex items-start gap-3 p-3">
    @isset($selectable)
        <input type="checkbox" name="lead_ids[]" value="{{ $lead->id }}" form="bulk-assign" class="mt-1.5 h-5 w-5 rounded border-slate-300">
    @endisset
    <a href="{{ route('crm.leads.show', $lead) }}" class="min-w-0 flex-1">
        <div class="flex items-center gap-2">
            <span class="truncate font-semibold">{{ $lead->name }}</span>
            @if($lead->temperature)
                <span class="chip {{ ['hot' => 'bg-red-100 text-red-700', 'warm' => 'bg-amber-100 text-amber-700', 'cold' => 'bg-sky-100 text-sky-700'][$lead->temperature] }}">{{ ucfirst($lead->temperature) }}</span>
            @endif
            @if($lead->re_enquiry_count)
                <span class="chip bg-violet-100 text-violet-700">Re-enquired ×{{ $lead->re_enquiry_count }}</span>
            @endif
        </div>
        <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-500">
            @include('crm.partials.stage-chip')
            @if($lead->source)<span>{{ $lead->source->name }}</span>@endif
            @if(isset($showOwner) && $lead->owner)<span>· {{ $lead->owner->name }}</span>@endif
        </div>
        @if($lead->next_follow_up_at)
            <div class="mt-1 text-sm {{ $overdue ? 'font-semibold text-red-600' : 'text-slate-600' }}">
                {{ $overdue ? 'Overdue' : 'Next' }}: {{ str_replace('_', ' ', $lead->next_follow_up_type ?? 'follow-up') }}, {{ $lead->next_follow_up_at->format('d M, h:i A') }}
            </div>
        @endif
    </a>
    <a href="tel:+{{ $lead->country_code }}{{ $lead->phone }}" class="rounded-full bg-emerald-50 p-2.5 text-emerald-700 active:bg-emerald-100" aria-label="Call {{ $lead->name }}">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.3a1 1 0 01.9.7l1.5 4.5a1 1 0 01-.5 1.2l-2.3 1.1a11 11 0 005.5 5.5l1.1-2.3a1 1 0 011.2-.5l4.5 1.5a1 1 0 01.7 1V19a2 2 0 01-2 2h-1C9.7 21 3 14.3 3 6V5z"/></svg>
    </a>
</div>

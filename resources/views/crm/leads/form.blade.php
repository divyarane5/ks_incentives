@extends('crm.layouts.app', ['title' => $lead->exists ? 'Edit lead' : 'Add lead', 'back' => $lead->exists ? route('crm.leads.show', $lead) : route('crm.leads.index')])
@section('content')
@php($e = $lead->enquiry)
<form method="POST" action="{{ $lead->exists ? route('crm.leads.update', $lead) : route('crm.leads.store') }}" class="space-y-4">
    @csrf
    @if($lead->exists) @method('PUT') @endif

    <div class="card space-y-3">
        <h2 class="font-semibold">Contact</h2>
        <div>
            <label class="label" for="name">Name *</label>
            <input class="input" id="name" name="name" value="{{ old('name', $lead->name) }}" required>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label" for="phone">Phone *</label>
                <input class="input" id="phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone', $lead->phone) }}" required>
            </div>
            <div>
                <label class="label" for="alt_phone">Other phone</label>
                <input class="input" id="alt_phone" name="alt_phone" type="tel" inputmode="tel" value="{{ old('alt_phone', $lead->alt_phone) }}">
            </div>
        </div>
        <div>
            <label class="label" for="email">Email</label>
            <input class="input" id="email" name="email" type="email" value="{{ old('email', $lead->email) }}">
        </div>
        @unless($lead->exists)
            <p class="text-xs text-slate-500">If this phone number is already in the CRM, the enquiry is added to that lead instead of creating a duplicate.</p>
        @endunless
    </div>

    <div class="card space-y-3">
        <h2 class="font-semibold">Source</h2>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label" for="source_id">Source *</label>
                <select class="input" id="source_id" name="source_id" required>
                    <option value="">Choose</option>
                    @foreach($sources as $s)<option value="{{ $s->id }}" @selected(old('source_id', $lead->source_id) == $s->id)>{{ $s->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="label" for="sub_source">Sub-source</label>
                @php($currentSub = old('sub_source', $lead->sub_source))
                <select class="input" id="sub_source" name="sub_source">
                    <option value="">None</option>
                    @foreach($sources as $s)
                        @foreach($s->subSources as $sub)
                            @if($sub->is_active || $sub->name === $currentSub)
                                <option value="{{ $sub->name }}" data-source="{{ $s->id }}" @selected($currentSub === $sub->name && old('source_id', $lead->source_id) == $s->id)>{{ $sub->name }}</option>
                            @endif
                        @endforeach
                    @endforeach
                </select>
                <script>
                    (() => {
                        const source = document.getElementById('source_id'), sub = document.getElementById('sub_source');
                        const show = () => {
                            for (const o of sub.options) {
                                if (!o.dataset.source) continue;
                                o.hidden = o.disabled = o.dataset.source !== source.value;
                                if (o.disabled && o.selected) sub.value = '';
                            }
                        };
                        source.addEventListener('change', show);
                        show();
                    })();
                </script>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label" for="temperature">Hot / warm / cold</label>
                <select class="input" id="temperature" name="temperature">
                    <option value="">Not set</option>
                    @foreach(['hot','warm','cold'] as $t)<option value="{{ $t }}" @selected(old('temperature', $lead->temperature) === $t)>{{ ucfirst($t) }}</option>@endforeach
                </select>
            </div>
            @if($canAssign && ! $lead->exists)
                <div>
                    <label class="label" for="owner_id">Assign to</label>
                    <select class="input" id="owner_id" name="owner_id">
                        <option value="">Me</option>
                        @foreach($owners as $o)<option value="{{ $o->id }}" @selected(old('owner_id') == $o->id)>{{ $o->name }}</option>@endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    <div class="card space-y-3">
        <h2 class="font-semibold">What they're looking for</h2>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label" for="enquired_for">Buy or rent *</label>
                <select class="input" id="enquired_for" name="enquired_for">
                    @foreach(['buy' => 'Buy', 'rent' => 'Rent'] as $k => $v)<option value="{{ $k }}" @selected(old('enquired_for', $e?->enquired_for ?? 'buy') === $k)>{{ $v }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="label" for="property_type_id">Property type</label>
                <select class="input" id="property_type_id" name="property_type_id">
                    <option value="">Any</option>
                    @foreach($propertyTypes as $group)
                        <optgroup label="{{ $group->name }}">
                            @foreach($group->children as $t)<option value="{{ $t->id }}" @selected(old('property_type_id', $e?->property_type_id) == $t->id)>{{ $t->name }}</option>@endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="bhk">BHK</label>
                <input class="input" id="bhk" name="bhk" type="number" step="0.5" min="0" inputmode="decimal" value="{{ old('bhk', $e?->bhk) }}">
            </div>
            <div>
                <label class="label" for="purpose">Purpose</label>
                <select class="input" id="purpose" name="purpose">
                    <option value="">Not set</option>
                    @foreach(['self_use' => 'Self use', 'investment' => 'Investment'] as $k => $v)<option value="{{ $k }}" @selected(old('purpose', $e?->purpose) === $k)>{{ $v }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="label" for="budget_min">Budget from (₹)</label>
                <input class="input" id="budget_min" name="budget_min" type="number" min="0" inputmode="numeric" value="{{ old('budget_min', $e?->budget_min) }}" placeholder="e.g. 5000000">
            </div>
            <div>
                <label class="label" for="budget_max">Budget to (₹)</label>
                <input class="input" id="budget_max" name="budget_max" type="number" min="0" inputmode="numeric" value="{{ old('budget_max', $e?->budget_max) }}">
            </div>
        </div>
        <div>
            <label class="label" for="locations">Preferred locations</label>
            <input class="input" id="locations" name="locations" value="{{ old('locations', $e?->locations) }}" placeholder="e.g. Thane West, Mira Road">
        </div>
        <div>
            <label class="label" for="project_ids">Projects interested in</label>
            <select class="input h-32" id="project_ids" name="project_ids[]" multiple>
                @php($selected = old('project_ids', $lead->exists ? $lead->projects->pluck('id')->all() : []))
                @foreach($projects as $p)<option value="{{ $p->id }}" @selected(in_array($p->id, $selected))>{{ $p->name }}</option>@endforeach
            </select>
        </div>
    </div>

    @unless($lead->exists)
        <div class="card">
            <label class="label" for="notes">Notes</label>
            <textarea class="input" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
        </div>
    @endunless

    <button class="btn w-full md:w-auto">{{ $lead->exists ? 'Save changes' : 'Add lead' }}</button>
</form>
@endsection

@extends('crm.layouts.app', ['title' => $import->file_name, 'back' => route('crm.imports.index')])
@section('content')
@if($import->isRunning())
    <meta http-equiv="refresh" content="3">
@endif

<div class="mb-3 flex items-center gap-2 text-sm text-slate-500">
    @include('crm.imports._status', ['import' => $import])
    <span>{{ number_format($import->total_rows) }} rows · uploaded {{ $import->created_at->format('d M Y, h:i A') }}</span>
</div>

@if($import->status === 'mapping')
    <form method="POST" action="{{ route('crm.imports.start', $import) }}" class="space-y-4">
        @csrf
        <div class="card">
            <h2 class="mb-1 font-semibold">Which column is which?</h2>
            <p class="mb-3 text-sm text-slate-500">We matched what we could from the column names. Check them, and leave anything you don't need as "Not in file".</p>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach($fields as $field => [$label])
                    <div>
                        <label class="label" for="map-{{ $field }}">{{ $label }} @if($field === 'phone')<span class="text-red-600">*</span>@endif</label>
                        <select id="map-{{ $field }}" name="map[{{ $field }}]" class="input">
                            <option value="">Not in file</option>
                            @foreach($import->headers as $i => $header)
                                <option value="{{ $i }}" @selected(old("map.$field", $import->mapping[$field] ?? null) === $i || (string) old("map.$field") === (string) $i)>{{ $header !== '' ? $header : 'Column '.($i + 1) }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card space-y-3">
            <h2 class="font-semibold">Options</h2>
            <div>
                <label class="label" for="default_source_id">Source for rows with no source</label>
                <select id="default_source_id" name="default_source_id" class="input">
                    <option value="">None</option>
                    @foreach($sources as $s)<option value="{{ $s->id }}" @selected(old('default_source_id', $defaultSourceId) == $s->id)>{{ $s->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="label" for="default_owner_id">Give leads with no matching owner to</label>
                <select id="default_owner_id" name="default_owner_id" class="input">
                    <option value="">Leave unassigned</option>
                    @foreach($owners as $o)<option value="{{ $o->id }}" @selected(old('default_owner_id') == $o->id)>{{ $o->name }}</option>@endforeach
                </select>
            </div>
            <label class="flex items-start gap-3 text-sm">
                <input type="checkbox" name="skip_closed" value="1" class="mt-0.5 h-5 w-5 rounded border-slate-300" @checked(old('skip_closed'))>
                <span>Skip closed leads (Not interested, Dropped, Booking cancel)</span>
            </label>
            <div>
                <label class="label" for="skip_before">Skip leads received before</label>
                <input id="skip_before" type="date" name="skip_before" value="{{ old('skip_before') }}" class="input">
                <p class="mt-1 text-xs text-slate-500">Optional. Booked and invoiced leads are always kept.</p>
            </div>
        </div>

        @if($import->preview)
            <div class="card overflow-x-auto p-0">
                <div class="px-4 pt-3 text-sm font-semibold">First rows of your file</div>
                <table class="mt-2 min-w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500"><tr>@foreach($import->headers as $h)<th class="px-3 py-2 font-medium whitespace-nowrap">{{ $h }}</th>@endforeach</tr></thead>
                    <tbody>
                    @foreach($import->preview as $row)
                        <tr class="border-t border-slate-100">@foreach($import->headers as $i => $h)<td class="px-3 py-2 whitespace-nowrap">{{ $row[$i] ?? '' }}</td>@endforeach</tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <button class="btn w-full sm:w-auto">Import {{ number_format($import->total_rows) }} rows</button>
    </form>
@else
    @if($import->isRunning())
        <div class="card mb-4">
            <div class="mb-2 flex justify-between text-sm"><span>{{ $import->status === 'queued' ? 'Waiting to start…' : 'Importing…' }}</span><span>{{ $import->percent() }}%</span></div>
            <div class="h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full bg-gold-500" style="width: {{ $import->percent() }}%"></div></div>
            <p class="mt-2 text-xs text-slate-500">You can leave this page. The import keeps running.</p>
            @if($import->status === 'queued' && $import->updated_at->lt(now()->subSeconds(30)))
                <p class="mt-2 rounded-lg bg-amber-50 px-3 py-2 text-sm text-amber-900">Still waiting? The background helper isn't running. On the computer running KeyLeads, open a window in the project folder and run <code class="font-mono">php artisan queue:listen</code>, then leave it open.</p>
            @endif
        </div>
    @endif
    @if($import->status === 'failed')
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">The import stopped. {{ $import->message }}</div>
    @endif

    <div class="mb-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="card p-3"><div class="text-2xl font-bold text-emerald-700">{{ number_format($import->created_count) }}</div><div class="text-xs text-slate-600">New leads added</div></div>
        <div class="card p-3"><div class="text-2xl font-bold">{{ number_format($import->re_enquiry_count) }}</div><div class="text-xs text-slate-600">Re-enquiries on existing leads</div></div>
        <div class="card p-3"><div class="text-2xl font-bold">{{ number_format($import->skipped_count) }}</div><div class="text-xs text-slate-600">Skipped</div></div>
        <div class="card p-3"><div class="text-2xl font-bold {{ $import->error_count ? 'text-red-600' : '' }}">{{ number_format($import->error_count) }}</div><div class="text-xs text-slate-600">Rows with problems</div></div>
    </div>

    @if($import->error_count)
        <a href="{{ route('crm.imports.problems', $import) }}" class="btn-light mb-4 w-full sm:w-auto">Download rows with problems</a>
    @endif

    @php($summary = $import->summary ?? [])
    @foreach(['skipped' => 'Why rows were skipped', 'owners' => 'Owners not found (left with the default owner)', 'stages' => 'Statuses not found (set to New)', 'sources' => 'Sources not found', 'projects' => 'Projects not found (create them, then link leads)'] as $key => $heading)
        @if(! empty($summary[$key]))
            @php($items = collect($summary[$key])->sortDesc())
            <div class="card mb-3">
                <h2 class="mb-2 text-sm font-semibold">{{ $heading }}</h2>
                <ul class="divide-y divide-slate-100 text-sm">
                    @foreach($items->take(25) as $name => $count)
                        <li class="flex justify-between gap-3 py-1.5"><span class="min-w-0 truncate">{{ $name }}</span><span class="text-slate-500">{{ number_format($count) }}</span></li>
                    @endforeach
                </ul>
                @if($items->count() > 25)<p class="mt-1 text-xs text-slate-500">and {{ $items->count() - 25 }} more</p>@endif
            </div>
        @endif
    @endforeach

    @if($import->status === 'done')
        <a href="{{ route('crm.leads.index', ['tab' => 'team']) }}" class="btn w-full sm:w-auto">See leads</a>
    @endif
@endif
@endsection

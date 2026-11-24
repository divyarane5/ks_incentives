@extends('crm.layouts.app', ['title' => 'Reports'])
@section('content')
<div class="-mx-4 mb-3 flex gap-2 overflow-x-auto px-4 pb-1">
    @foreach($reports as $key => $label)
        <a href="{{ route('crm.reports.index', ['report' => $key] + request()->only('period', 'from', 'to')) }}"
           class="shrink-0 rounded-full px-4 py-1.5 text-sm font-medium {{ $report === $key ? 'bg-brand-700 text-white' : 'border border-slate-200 bg-white text-slate-700' }}">{{ $label }}</a>
    @endforeach
</div>

<form method="GET" class="mb-3 flex flex-wrap items-end gap-2" id="period-form">
    <input type="hidden" name="report" value="{{ $report }}">
    <div class="min-w-36 flex-1">
        <label class="label" for="period">{{ in_array($report, ['visits', 'calls']) ? ($report === 'calls' ? 'Calls made' : 'Planned for') : 'Leads received' }}</label>
        <select id="period" name="period" class="input" onchange="document.getElementById('custom-dates').hidden = this.value !== 'custom'; if (this.value !== 'custom') this.form.submit()">
            @foreach($periods as $key => $label)<option value="{{ $key }}" @selected($period === $key)>{{ $label }}</option>@endforeach
        </select>
    </div>
    <div id="custom-dates" class="flex flex-1 basis-full items-end gap-2 sm:basis-auto" @if($period !== 'custom') hidden @endif>
        <input type="date" name="from" value="{{ $from?->format('Y-m-d') }}" class="input" aria-label="From">
        <input type="date" name="to" value="{{ $to?->format('Y-m-d') }}" class="input" aria-label="To">
        <button class="btn shrink-0">Show</button>
    </div>
</form>

@php
    $listParams = ['tab' => 'all'] + ($from ? ['from' => $from->format('Y-m-d')] : []) + ($to ? ['to' => $to->format('Y-m-d')] : []);
    $cellLink = function ($row, $col) use ($listParams, $table) {
        if (! $table['linkable']) return null;
        $params = $listParams + $row['filter'];
        if ($col['key'] === 'overdue') $params['tab'] = 'overdue';
        if (isset($col['stage'])) $params['stage'] = $col['stage'];
        return route('crm.leads.index', $params);
    };
@endphp

@if(empty($table['rows']))
    <div class="card text-center text-sm text-slate-500">Nothing to show for this period.</div>
@else
    <div class="card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-xs text-slate-500">
                    <tr>
                        <th class="sticky left-0 z-10 bg-slate-50 px-3 py-2 text-left font-medium"></th>
                        @foreach($table['columns'] as $col)
                            <th class="px-3 py-2 text-right font-medium whitespace-nowrap">
                                @isset($col['color'])<span class="mr-1 inline-block h-2 w-2 rounded-full" style="background: {{ $col['color'] }}"></span>@endisset{{ $col['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($table['rows'] as $row)
                        <tr>
                            <th class="sticky left-0 z-10 max-w-40 truncate bg-white px-3 py-2 text-left font-medium">{{ $row['label'] }}</th>
                            @foreach($table['columns'] as $col)
                                @php($v = $row['cells'][$col['key']])
                                <td class="px-3 py-2 text-right tabular-nums {{ $col['key'] === 'overdue' && $v ? 'text-red-600 font-semibold' : '' }} {{ $loop->first ? 'font-semibold' : '' }}">
                                    @if($v && ($href = $cellLink($row, $col)))
                                        <a href="{{ $href }}" class="underline decoration-slate-300 underline-offset-2 hover:text-brand-700">{{ number_format($v) }}</a>
                                    @else
                                        <span class="{{ $v ? '' : 'text-slate-300' }}">{{ number_format($v) }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                @if($table['totals'])
                    <tfoot class="border-t-2 border-slate-200 bg-slate-50 font-semibold">
                        <tr>
                            <th class="sticky left-0 z-10 bg-slate-50 px-3 py-2 text-left">Total</th>
                            @foreach($table['columns'] as $col)
                                <td class="px-3 py-2 text-right tabular-nums">{{ number_format($table['totals'][$col['key']]) }}</td>
                            @endforeach
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
    <div class="mt-2 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
        <span>{{ $table['note'] ?? ($table['linkable'] ? 'Tap a number to see those leads.' : '') }}</span>
        <a href="{{ route('crm.reports.index', request()->query() + ['report' => $report, 'export' => 'csv']) }}" class="font-medium text-brand-700 underline">Download for Excel</a>
    </div>
@endif
@endsection

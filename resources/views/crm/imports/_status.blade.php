@php($styles = ['mapping' => 'bg-amber-100 text-amber-800', 'queued' => 'bg-brand-50 text-brand-700', 'running' => 'bg-brand-50 text-brand-700', 'done' => 'bg-emerald-100 text-emerald-800', 'failed' => 'bg-red-100 text-red-700'])
@php($labels = ['mapping' => 'Check columns', 'queued' => 'Waiting to start', 'running' => 'Importing', 'done' => 'Done', 'failed' => 'Stopped'])
<span class="chip shrink-0 {{ $styles[$import->status] ?? '' }}">{{ $labels[$import->status] ?? $import->status }}</span>

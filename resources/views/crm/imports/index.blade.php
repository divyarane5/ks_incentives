@extends('crm.layouts.app', ['title' => 'Upload leads', 'back' => route('crm.leads.index')])
@section('content')
<form method="POST" action="{{ route('crm.imports.store') }}" enctype="multipart/form-data" class="card mb-5 space-y-3">
    @csrf
    <div>
        <label class="label" for="file">Excel or CSV file</label>
        <input id="file" type="file" name="file" accept=".xlsx,.csv" required class="input file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-700">
        <p class="mt-1 text-xs text-slate-500">First row: column names. Leads from row 2. Works with the Leadrat lead export too.
            <a href="{{ route('crm.imports.template') }}" class="font-medium text-brand-700 underline">Download a blank sheet</a></p>
    </div>
    <button class="btn w-full sm:w-auto">Upload and check columns</button>
    <p class="text-xs text-slate-500">Nothing is saved until you check the columns on the next screen. Phone numbers that already exist are added to that lead as a re-enquiry, never as a duplicate.</p>
</form>

<h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-slate-500">Past uploads</h2>
<div class="space-y-2">
    @forelse($imports as $import)
        <a href="{{ route('crm.imports.show', $import) }}" class="card block p-3">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 truncate font-semibold">{{ $import->file_name }}</div>
                @include('crm.imports._status', ['import' => $import])
            </div>
            <div class="mt-1 text-sm text-slate-500">
                {{ $import->created_at->format('d M Y, h:i A') }} · {{ $import->user?->name }} ·
                @if($import->status === 'mapping')
                    {{ number_format($import->total_rows) }} rows, not started
                @else
                    {{ number_format($import->created_count) }} added, {{ number_format($import->re_enquiry_count) }} re-enquiries
                @endif
            </div>
        </a>
    @empty
        <div class="card text-center text-sm text-slate-500">No uploads yet.</div>
    @endforelse
</div>
<div class="mt-4">{{ $imports->links() }}</div>
@endsection

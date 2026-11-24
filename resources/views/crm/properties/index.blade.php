@extends('crm.layouts.app', ['title' => 'Properties', 'back' => route('crm.projects.index')])
@section('content')
<div class="mb-3 flex gap-2">
    <form method="GET" class="flex flex-1 gap-2">
        <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search title or location">
        <select name="status" class="input w-auto"><option value="">Any status</option>
            @foreach(['available','booked','sold'] as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach
        </select>
        <button class="btn-light shrink-0">Go</button>
    </form>
    @if(auth()->user()->isAdmin())<a href="{{ route('crm.properties.create') }}" class="btn shrink-0">+ Add</a>@endif
</div>
<div class="space-y-2">
    @forelse($properties as $property)
        @include('crm.properties._card')
    @empty
        <div class="card text-center text-sm text-slate-500">No properties yet.</div>
    @endforelse
</div>
<div class="mt-4">{{ $properties->onEachSide(1)->links() }}</div>
@endsection

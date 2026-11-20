@extends('crm.layouts.app', ['title' => 'Edit project', 'back' => route('crm.projects.show', $project)])
@section('content')
<form method="POST" action="{{ route('crm.projects.update', $project) }}" class="card space-y-3">
    @csrf
    @method('PUT')
    <div>
        <div class="text-lg font-semibold">{{ $project->name }}</div>
        <div class="text-sm text-slate-500">{{ $project->developer?->name ?? 'No developer' }} · name, developer and brokerage are changed in the back office</div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="label" for="category">Type *</label>
            <select class="input" id="category" name="category">
                @foreach(['residential' => 'Residential', 'commercial' => 'Commercial', 'agricultural' => 'Agricultural'] as $k => $v)<option value="{{ $k }}" @selected(old('category', $project->category ?? 'residential') === $k)>{{ $v }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="label" for="launch_status">Status</label>
            <select class="input" id="launch_status" name="launch_status">
                <option value="">Not set</option>
                @foreach(['upcoming' => 'Upcoming', 'pre_launch' => 'Pre-launch', 'launch' => 'Launch', 'ongoing' => 'Under construction', 'ready_to_move' => 'Ready to move', 'resale' => 'Resale'] as $k => $v)<option value="{{ $k }}" @selected(old('launch_status', $project->launch_status) === $k)>{{ $v }}</option>@endforeach
            </select>
        </div>
        <div><label class="label" for="location">Location</label><input class="input" id="location" name="location" value="{{ old('location', $project->location) }}"></div>
        <div><label class="label" for="city">City</label><input class="input" id="city" name="city" value="{{ old('city', $project->city) }}"></div>
        <div><label class="label" for="min_price">Price from (₹)</label><input class="input" id="min_price" name="min_price" type="number" min="0" inputmode="numeric" value="{{ old('min_price', $project->min_price) }}"></div>
        <div><label class="label" for="max_price">Price to (₹)</label><input class="input" id="max_price" name="max_price" type="number" min="0" inputmode="numeric" value="{{ old('max_price', $project->max_price) }}"></div>
        <div><label class="label" for="possession_date">Possession</label><input class="input" id="possession_date" name="possession_date" type="date" value="{{ old('possession_date', $project->possession_date?->format('Y-m-d')) }}"></div>
    </div>
    <div><label class="label" for="description">Description</label><textarea class="input" id="description" name="description" rows="3">{{ old('description', $project->description) }}</textarea></div>
    <button class="btn w-full md:w-auto">Save</button>
</form>
@endsection

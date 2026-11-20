@extends('crm.layouts.app', ['title' => $property->exists ? 'Edit property' : 'Add property', 'back' => route('crm.properties.index')])
@section('content')
<form method="POST" action="{{ $property->exists ? route('crm.properties.update', $property) : route('crm.properties.store') }}" class="card space-y-3">
    @csrf
    @if($property->exists) @method('PUT') @endif
    <div><label class="label" for="title">Title *</label><input class="input" id="title" name="title" value="{{ old('title', $property->title) }}" required placeholder="e.g. 2 BHK, Tower B, 12th floor"></div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="label" for="project_id">Project</label>
            <select class="input" id="project_id" name="project_id"><option value="">None (resale / standalone)</option>
                @foreach($projects as $p)<option value="{{ $p->id }}" @selected(old('project_id', $property->project_id) == $p->id)>{{ $p->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="label" for="property_type_id">Type</label>
            <select class="input" id="property_type_id" name="property_type_id"><option value="">Not set</option>
                @foreach($propertyTypes as $group)<optgroup label="{{ $group->name }}">
                    @foreach($group->children as $t)<option value="{{ $t->id }}" @selected(old('property_type_id', $property->property_type_id) == $t->id)>{{ $t->name }}</option>@endforeach
                </optgroup>@endforeach
            </select>
        </div>
        <div>
            <label class="label" for="listing_for">For *</label>
            <select class="input" id="listing_for" name="listing_for">
                @foreach(['sale' => 'Sale', 'rent' => 'Rent'] as $k => $v)<option value="{{ $k }}" @selected(old('listing_for', $property->listing_for ?? 'sale') === $k)>{{ $v }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="label" for="status">Status *</label>
            <select class="input" id="status" name="status">
                @foreach(['available' => 'Available', 'booked' => 'Booked', 'sold' => 'Sold'] as $k => $v)<option value="{{ $k }}" @selected(old('status', $property->status ?? 'available') === $k)>{{ $v }}</option>@endforeach
            </select>
        </div>
        <div><label class="label" for="bhk">BHK</label><input class="input" id="bhk" name="bhk" type="number" step="0.5" min="0" value="{{ old('bhk', $property->bhk) }}"></div>
        <div><label class="label" for="price">Price (₹)</label><input class="input" id="price" name="price" type="number" min="0" inputmode="numeric" value="{{ old('price', $property->price) }}"></div>
        <div><label class="label" for="carpet_area">Carpet area (sq ft)</label><input class="input" id="carpet_area" name="carpet_area" type="number" step="0.01" min="0" value="{{ old('carpet_area', $property->carpet_area) }}"></div>
        <div><label class="label" for="unit_number">Unit no.</label><input class="input" id="unit_number" name="unit_number" value="{{ old('unit_number', $property->unit_number) }}"></div>
        <div><label class="label" for="location">Location</label><input class="input" id="location" name="location" value="{{ old('location', $property->location) }}"></div>
        <div><label class="label" for="city">City</label><input class="input" id="city" name="city" value="{{ old('city', $property->city) }}"></div>
        <div><label class="label" for="owner_name">Owner name</label><input class="input" id="owner_name" name="owner_name" value="{{ old('owner_name', $property->owner_name) }}"></div>
        <div><label class="label" for="owner_phone">Owner phone</label><input class="input" id="owner_phone" name="owner_phone" type="tel" value="{{ old('owner_phone', $property->owner_phone) }}"></div>
    </div>
    <button class="btn w-full md:w-auto">Save</button>
</form>
@endsection

<div class="card p-3">
    <div class="flex items-center justify-between gap-2">
        <span class="font-semibold">{{ $property->title }}</span>
        <span class="chip {{ ['available' => 'bg-emerald-100 text-emerald-700', 'booked' => 'bg-amber-100 text-amber-700', 'sold' => 'bg-slate-200 text-slate-600'][$property->status] ?? '' }}">{{ ucfirst($property->status) }}</span>
    </div>
    <div class="text-sm text-slate-500">
        {{ $property->listing_for === 'rent' ? 'For rent' : 'For sale' }}
        @if($property->type) · {{ $property->type->name }}@endif
        @if($property->bhk) · {{ rtrim(rtrim($property->bhk, '0'), '.') }} BHK @endif
        @if($property->carpet_area) · {{ rtrim(rtrim($property->carpet_area, '0'), '.') }} sq ft @endif
    </div>
    <div class="mt-1 flex items-center justify-between text-sm">
        <span class="font-medium">{{ \App\Support\Money::short($property->price) }}</span>
        @if(auth()->user()->isAdmin())<a href="{{ route('crm.properties.edit', $property) }}" class="text-brand-700">Edit</a>@endif
    </div>
</div>

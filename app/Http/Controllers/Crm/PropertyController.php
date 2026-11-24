<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $properties = Property::with(['project', 'type'])
            ->when($request->query('q'), fn ($q, $v) => $q->where(fn ($w) => $w->where('title', 'like', "%{$v}%")->orWhere('location', 'like', "%{$v}%")))
            ->when($request->query('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($request->query('listing_for'), fn ($q, $v) => $q->where('listing_for', $v))
            ->latest()->paginate(25)->withQueryString();

        return view('crm.properties.index', compact('properties'));
    }

    public function create(Request $request)
    {
        return view('crm.properties.form', $this->formData() + ['property' => new Property(['project_id' => $request->query('project')])]);
    }

    public function store(Request $request)
    {
        Property::create($this->validated($request) + ['created_by_id' => $request->user()->id]);

        return redirect()->route('crm.properties.index')->with('status', 'Property added.');
    }

    public function edit(Property $property)
    {
        return view('crm.properties.form', $this->formData() + ['property' => $property]);
    }

    public function update(Request $request, Property $property)
    {
        $property->update($this->validated($request));

        return redirect()->route('crm.properties.index')->with('status', 'Property updated.');
    }

    private function formData(): array
    {
        return [
            'projects' => Project::active()->orderBy('name')->get(['id', 'name']),
            'propertyTypes' => PropertyType::with('children')->whereNull('parent_id')->orderBy('sort_order')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'project_id' => ['nullable', Rule::exists('projects', 'id')],
            'property_type_id' => ['nullable', Rule::exists('property_types', 'id')],
            'listing_for' => ['required', Rule::in(['sale', 'rent'])],
            'bhk' => ['nullable', 'numeric', 'between:0,20'],
            'price' => ['nullable', 'integer', 'min:0'],
            'carpet_area' => ['nullable', 'numeric', 'min:0'],
            'unit_number' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:200'],
            'city' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['available', 'booked', 'sold'])],
            'owner_name' => ['nullable', 'string', 'max:100'],
            'owner_phone' => ['nullable', 'string', 'max:20'],
        ]);
    }
}

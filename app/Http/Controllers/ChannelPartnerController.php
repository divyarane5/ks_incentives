<?php

namespace App\Http\Controllers;

use App\Models\ChannelPartner;
use App\Models\Location; // ✅ Import added
use Illuminate\Http\Request;
use DataTables;
use Auth; 

class ChannelPartnerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ChannelPartner::query();

            return DataTables::of($data)
                ->addColumn('firm_name', fn($row) => $row->firm_name)
                ->addColumn('owner_name', fn($row) => $row->owner_name)
                ->addColumn('contact', fn($row) => $row->contact)
                ->addColumn('rera_number', fn($row) => $row->rera_number)
                ->addColumn('operational_locations', function($row) {
                    $locs = is_array($row->operational_locations) ? $row->operational_locations : json_decode($row->operational_locations, true);
                    return $locs ? implode(', ', \App\Models\Location::whereIn('id', $locs)->pluck('name')->toArray()) : '-';
                })
                ->addColumn('office_locations', function($row) {
                    $locs = is_array($row->office_locations) ? $row->office_locations : json_decode($row->office_locations, true);
                    return $locs ? implode(', ', \App\Models\Location::whereIn('id', $locs)->pluck('name')->toArray()) : '-';
                })
                ->addColumn('sourcing_manager', fn($row) => $row->sourcing_manager ?? '-')
                ->addColumn('acquisition_channel', fn($row) => ucfirst($row->acquisition_channel))
                ->addColumn('property_type', fn($row) => ucfirst($row->property_type))
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->can('channel-partner-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('channel_partners.edit', $row->id).'">
                                        <i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }
                    if (auth()->user()->can('channel-partner-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteChannelPartner('.$row->id.')">
                                        <i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('channel_partners.destroy', $row->id).'" method="POST" class="d-none">
                                        '.csrf_field().method_field('delete').'
                                    </form>';
                    }
                    return $actions ? '<div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">'.$actions.'</div>
                                    </div>' : '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('channel_partners.index');
    }




    public function create()
    {
        $locations = Location::all();
        return view('channel_partners.create', compact('locations'));
    }

    // public function store(Request $request)
    // {
    //     // Validate the request
    //     $request->validate([
    //         'firm_name' => 'required|string|max:255',
    //         'owner_name' => 'required|string|max:255',
    //         'contact' => 'required|string|max:20',
    //         'rera_number' => 'nullable|string|max:255',
    //         'acquisition_channel' => 'required|in:telecalling,digital,reference,BTL',
    //         'property_type' => 'required|in:commercial,residential',
    //         'sourcing_manager' => 'nullable|string|max:255',
    //         'operational_locations' => 'nullable|array',
    //         'office_locations' => 'nullable|array',
    //     ]);

    //     // Prepare data
    //     $data = $request->only([
    //         'firm_name', 'owner_name', 'contact', 'rera_number',
    //         'acquisition_channel', 'property_type', 'sourcing_manager'
    //     ]);

    //     // print_r($request->operational_locations); 
    //     // print_r($request->office_locations); exit;

    //     // Encode location arrays as JSON, or store empty array
    //     $data['operational_locations'] = $request->operational_locations ?? [];
    //     $data['office_locations'] = $request->office_locations ?? [];

    //     //print_r($data); exit;
    //     // If your model casts JSON, no need to json_encode manually
    //     ChannelPartner::create($data);

    //     return redirect()->route('channel_partners.index')->with('success', 'Channel Partner added successfully.');
    // }

    public function store(Request $request)
    {
        // Get submitted arrays or empty
        $operationalLocations = $request->operational_locations ?? [];
        $officeLocations = $request->office_locations ?? [];

        // Handle new locations
        foreach (['operational_locations' => &$operationalLocations, 'office_locations' => &$officeLocations] as $field => &$arr) {
            $newField = 'new_' . $field;
            if ($request->has($newField)) {
                foreach ($request->input($newField) as $locName) {
                    // Create new location and get its ID
                    $location = Location::firstOrCreate(
                        ['name' => $locName],
                        ['created_by' => Auth::id() ?? 1]
                    );
                    $arr[] = $location->id; // append ID only
                }
            }

            // Ensure all entries are integers (convert existing IDs if needed)
            $arr = array_map(function($val) {
                return (int) $val;
            }, $arr);
        }

        // Save Channel Partner
        ChannelPartner::create([
            'firm_name' => $request->firm_name,
            'owner_name' => $request->owner_name,
            'contact' => $request->contact,
            'rera_number' => $request->rera_number,
            'operational_locations' => $operationalLocations,
            'office_locations' => $officeLocations,
            'sourcing_manager' => $request->sourcing_manager,
            'acquisition_channel' => $request->acquisition_channel,
            'property_type' => $request->property_type,
        ]);

        return redirect()->route('channel_partners.index')->with('success', 'Channel Partner added!');
    }




    public function edit($id)
    {
        $channelPartner = ChannelPartner::findOrFail($id);

        // Fetch locations for dropdown or other usage
        $locations = Location::all(); // Or filtered list if needed

        return view('channel_partners.edit', compact('channelPartner', 'locations'));
    }
    public function update(Request $request, ChannelPartner $channelPartner)
    {
        $request->validate([
            'firm_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'acquisition_channel' => 'required|in:telecalling,digital,reference,BTL',
            'property_type' => 'required|in:commercial,residential',
            'operational_locations' => 'nullable|array',
            'office_locations' => 'nullable|array',
        ]);

        $data = $request->all();
        $data['operational_locations'] = $request->operational_locations ? json_encode($request->operational_locations) : null;
        $data['office_locations'] = $request->office_locations ? json_encode($request->office_locations) : null;

        $channelPartner->update($data);

        return redirect()->route('channel_partners.index')->with('success', 'Channel Partner updated successfully.');
    }

    public function destroy(ChannelPartner $channelPartner)
    {
        $channelPartner->delete();
        return redirect()->route('channel_partners.index')->with('success', 'Channel Partner deleted successfully.');
    }
}

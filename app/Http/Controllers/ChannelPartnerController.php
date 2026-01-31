<?php

namespace App\Http\Controllers;

use App\Models\ChannelPartner;
use App\Models\Location; // ✅ Import added
use Illuminate\Http\Request;
use DataTables;
use Auth; 
use App\Traits\UserHierarchyTrait;

class ChannelPartnerController extends Controller
{   
    use UserHierarchyTrait;

    public function index(Request $request)
    {   
         // ✅ DEFINE USER ONCE (IMPORTANT)
        $user = auth()->user();

        if ($request->ajax()) {
            $user = auth()->user();

            $data = ChannelPartner::with('sourcingManager');

            // Get all user IDs under current user including self
            $accessibleUserIds = $this->getAccessibleUserIds($user);

            // Apply filtering
            $data->whereIn('sourcing_manager', $accessibleUserIds);

            // ✅ Sourcing manager filter (from dropdown)
            if ($request->filled('sourcing_manager_id')) {
                $data->where('sourcing_manager', $request->sourcing_manager_id);
            }
            
            // // Optional: filter by a specific user (for TL/Admin)
            // if ($request->filled('user_id')) {
            //     $data->where('sourcing_manager', $request->user_id);
            // }

            return DataTables::of($data)
                ->addColumn('firm_name', fn($row) => $row->firm_name)
                ->addColumn('owner_name', fn($row) => $row->owner_name)
                ->addColumn('cp_executive', fn($row) => $row->cp_executive)
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
                ->addColumn('sourcing_manager', fn($row) => $row->sourcingManager->name ?? '-')
                ->addColumn('acquisition_channel', function($row) {
                    $channels = is_array($row->acquisition_channel)
                        ? $row->acquisition_channel
                        : json_decode($row->acquisition_channel, true);
                    return $channels ? implode(', ', $channels) : '-';
                })
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
        // Dropdown data
         // ✅ DROPDOWN DATA (FIXED)
        $sourcingManagers = $this->getUsersForDropdown(
                auth()->user(),
                ['AI'], // business units
                true            // APPLY hierarchy for filters
            );

        return view('channel_partners.index', compact('sourcingManagers'));
    }

    // /**
    //  * Get all accessible user IDs for the current user
    //  */
    // private function getAccessibleUserIds($user)
    // {
    //     // Super Admin sees all users
    //     if ($user->role == 'superadmin') {
    //         return \App\Models\User::pluck('id')->toArray();
    //     }

    //     // Start with current user ID
    //     $ids = [$user->id];

    //     // Get all users in hierarchy under current user
    //     $teamIds = \App\Models\User::whereNotNull('reporting_manager_id')
    //         ->get()
    //         ->filter(function($u) use ($user) {
    //             // Check if the user is under current user's hierarchy
    //             return $this->isUnderHierarchy($u, $user->id);
    //         })
    //         ->pluck('id')
    //         ->toArray();

    //     return array_merge($ids, $teamIds);
    // }

    /**
     * Check if a user is under the hierarchy of a given manager
     */
    private function isUnderHierarchy($user, $managerId)
    {
        $current = $user;
        while ($current->reporting_manager_id) {
            if ($current->reporting_manager_id == $managerId) {
                return true;
            }
            $current = \App\Models\User::find($current->reporting_manager_id);
            if (!$current) break;
        }
        return false;
    }


    public function create()
    {
        $locations = Location::all();

        // Fetch only users from business unit "Alterra India"
        $users = \App\Models\User::whereHas('businessUnit', function ($query) {
            $query->where('code', 'AI');
        })->get(['id', 'name']);

        // Pass both locations and users to the view
        return view('channel_partners.create', compact('locations', 'users'));
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
        // ✅ Validate incoming data
        $validated = $request->validate([
            'firm_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'contact' => 'required|string|max:15',
            'rera_number' => 'nullable|string|max:255',
            'sourcing_manager' => 'nullable|exists:users,id',
            'acquisition_channel' => 'required|array|min:1', // multi-select
            'acquisition_channel.*' => 'in:telecalling,digital,reference,BTL',
            'property_type' => 'required|in:commercial,residential,both',
            'cp_executive' => 'nullable|string|max:255', // ✅ Add this line
            'operational_locations' => 'nullable|array',
            'office_locations' => 'nullable|array',
            'new_operational_locations' => 'nullable|array',
            'new_office_locations' => 'nullable|array',
        ]);

        // ✅ Handle locations
        $operationalLocations = $validated['operational_locations'] ?? [];
        $officeLocations = $validated['office_locations'] ?? [];

        foreach ([
            'operational_locations' => &$operationalLocations,
            'office_locations' => &$officeLocations
        ] as $field => &$arr) {

            $newField = 'new_' . $field;
            if ($request->has($newField)) {
                foreach ($request->input($newField) as $locName) {
                    if (trim($locName) !== '') {
                        $location = Location::firstOrCreate(
                            ['name' => trim($locName)],
                            ['created_by' => Auth::id() ?? 1]
                        );
                        $arr[] = $location->id;
                    }
                }
            }

            // Convert all IDs to integers
            $arr = array_map('intval', $arr);
        }

        // ✅ Create Channel Partner
        ChannelPartner::create([
            'firm_name' => $validated['firm_name'],
            'owner_name' => $validated['owner_name'],
            'contact' => $validated['contact'],
            'rera_number' => $validated['rera_number'] ?? null,
            'operational_locations' => $operationalLocations,
            'office_locations' => $officeLocations,
            'sourcing_manager' => $validated['sourcing_manager'] ?? null,
            'acquisition_channel' => json_encode($validated['acquisition_channel']), // <-- convert to JSON
            'property_type' => $validated['property_type'],
            'cp_executive' => $validated['cp_executive'],
            'created_by' => Auth::id() ?? 1,
        ]);

        return redirect()
            ->route('channel_partners.index')
            ->with('success', 'Channel Partner added successfully!');
    }




   public function edit($id)
    {
        $channelPartner = ChannelPartner::findOrFail($id);

        // Decode location arrays (stored as JSON)
        $operationalLocIds = is_array($channelPartner->operational_locations)
            ? $channelPartner->operational_locations
            : json_decode($channelPartner->operational_locations, true) ?? [];

        $officeLocIds = is_array($channelPartner->office_locations)
            ? $channelPartner->office_locations
            : json_decode($channelPartner->office_locations, true) ?? [];

        // Fetch location names for pre-selection
        $operationalLocations = Location::whereIn('id', $operationalLocIds)->get(['id', 'name']);
        $officeLocations = Location::whereIn('id', $officeLocIds)->get(['id', 'name']);

        // Get sourcing managers (users)
        //$users = \App\Models\User::select('id', 'name')->get();
        $users = \App\Models\User::whereHas('businessUnit', function ($query) {
            $query->where('name', 'Alterra India');
        })->get(['id', 'name']);
        return view('channel_partners.edit', compact(
            'channelPartner',
            'users',
            'operationalLocations',
            'officeLocations'
        ));
    }

    public function update(Request $request, ChannelPartner $channelPartner)
    {
        // ✅ Validate incoming data
        $validated = $request->validate([
            'firm_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'cp_executive' => 'nullable|string|max:255',
            'contact' => 'required|string|max:20',
            'rera_number' => 'nullable|string|max:255',
            'sourcing_manager' => 'nullable|exists:users,id',
            'property_type' => 'required|in:commercial,residential,both',
            'acquisition_channel' => 'required|array',
            'acquisition_channel.*' => 'in:telecalling,digital,reference,BTL',
            'operational_locations' => 'nullable|array',
            'office_locations' => 'nullable|array',
            'new_operational_locations' => 'nullable|array',
            'new_office_locations' => 'nullable|array',
        ]);

        // Initialize location arrays
        $operationalLocs = $validated['operational_locations'] ?? [];
        $officeLocs = $validated['office_locations'] ?? [];

        // Handle new custom operational locations
        if (!empty($validated['new_operational_locations'])) {
            foreach ($validated['new_operational_locations'] as $locName) {
                if (trim($locName) !== '') {
                    $loc = Location::firstOrCreate(['name' => trim($locName)]);
                    $operationalLocs[] = $loc->id;
                }
            }
        }

        // Handle new custom office locations
        if (!empty($validated['new_office_locations'])) {
            foreach ($validated['new_office_locations'] as $locName) {
                if (trim($locName) !== '') {
                    $loc = Location::firstOrCreate(['name' => trim($locName)]);
                    $officeLocs[] = $loc->id;
                }
            }
        }

        // ✅ Prepare data for update
        $data = [
            'firm_name' => $validated['firm_name'],
            'owner_name' => $validated['owner_name'],
            'cp_executive' => $validated['cp_executive'] ?? null,
            'contact' => $validated['contact'],
            'rera_number' => $validated['rera_number'] ?? null,
            'sourcing_manager' => $validated['sourcing_manager'] ?? null,
            'property_type' => $validated['property_type'],
            'acquisition_channel' => json_encode($validated['acquisition_channel']),
            'operational_locations' => !empty($operationalLocs) ? json_encode($operationalLocs) : null,
            'office_locations' => !empty($officeLocs) ? json_encode($officeLocs) : null,
        ];

        // Update channel partner
        $channelPartner->update($data);

        return redirect()->route('channel_partners.index')
            ->with('success', 'Channel Partner updated successfully.');
    }



    public function destroy(ChannelPartner $channelPartner)
    {
        $channelPartner->delete();
        return redirect()->route('channel_partners.index')->with('success', 'Channel Partner deleted successfully.');
    }

    // Show public form
    public function createPublic()
    {
        // Get all users who can be sourcing managers
        $users = \App\Models\User::all(); // Or filter based on role if needed

        return view('channel_partners.public_create', compact('users'));
    }

    // Handle public form submission
    public function storePublic(Request $request)
    {
        $validated = $request->validate([
            'firm_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:20',
            'rera_number' => 'nullable|string|max:255',

            'operational_locations' => 'nullable|array',
            'office_locations' => 'nullable|array',
            'new_operational_locations' => 'nullable|array',
            'new_office_locations' => 'nullable|array',

            'sourcing_manager' => 'nullable|integer|exists:users,id',
            'acquisition_channel' => 'nullable|array',

            'cp_executive' => 'nullable|string|max:255',
            'property_type' => 'required|string',
        ]);

        // -------------------------
        // PROCESS LOCATIONS (same as admin)
        // -------------------------
        $operationalLocations = $validated['operational_locations'] ?? [];
        $officeLocations = $validated['office_locations'] ?? [];

        foreach ([
            'operational_locations' => &$operationalLocations,
            'office_locations' => &$officeLocations
        ] as $field => &$arr) {

            $newField = 'new_' . $field;

            if ($request->has($newField)) {
                foreach ($request->input($newField) as $locName) {

                    if (trim($locName) !== '') {

                        $location = Location::firstOrCreate(
                            ['name' => trim($locName)],
                            ['created_by' => 1] // Public user
                        );

                        $arr[] = $location->id; // store ID not text
                    }
                }
            }

            // Make sure all are integers
            $arr = array_map('intval', $arr);
        }

        // -------------------------
        // SAVE CHANNEL PARTNER
        // -------------------------
        ChannelPartner::create([
            'firm_name' => $validated['firm_name'],
            'owner_name' => $validated['owner_name'],
            'contact' => $validated['contact'] ?? null,
            'rera_number' => $validated['rera_number'] ?? null,

            'operational_locations' => $operationalLocations,
            'office_locations' => $officeLocations,

            'sourcing_manager' => $validated['sourcing_manager'] ?? null,
            'acquisition_channel' => json_encode($validated['acquisition_channel'] ?? []),

            'property_type' => $validated['property_type'],
            'cp_executive' => $validated['cp_executive'] ?? null,

            'created_by' => 1, // Public form
        ]);

        return back()->with('success', 'Thank you! Your request has been submitted.');
    }

    public function quickStore(Request $request)
    {
        $request->validate([
            'firm_name' => 'required|string|max:255',
        ]);

        $cp = ChannelPartner::create([
            'firm_name' => $request->firm_name,
            'owner_name' => "NA",
            'status'    => 0 // optional: mark as incomplete
        ]);

        return response()->json([
            'success'   => true,
            'id'        => $cp->id,
            'firm_name' => $cp->firm_name
        ]);
    }

}   

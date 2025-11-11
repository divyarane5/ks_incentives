<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Http\Requests\LocationRequest;
use DataTables;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:location-view', ['only' => ['index']]);
        $this->middleware('permission:location-create', ['only' => ['create','store']]);
        $this->middleware('permission:location-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:location-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Location::query();
            return DataTables::of($data)
                ->addColumn('name', fn($row) => $row->name)
                ->addColumn('status', fn($row) => $row->status)
                ->addColumn('created_at', fn($row) => date("d-m-Y", strtotime($row->created_at)))
                ->addColumn('updated_at', fn($row) => $row->updated_at ? date("d-m-Y", strtotime($row->updated_at)) : '-')
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->can('location-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('location.edit', $row->id).'">
                                        <i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }
                    if (auth()->user()->can('location-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteLocation('.$row->id.')">
                                        <i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('location.destroy', $row->id).'" method="POST" class="d-none">
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

        return view('location.index');
    }

    public function create()
    {
        return view('location.create');
    }

    public function store(LocationRequest $request)
    {
        // \Log::info($request->all());
        //dd($request->all()); // will show submitted data
        $location = new Location();
        $location->city = $request->city;
        $location->locality = $request->locality;
        $location->name = $request->city . ($request->locality ? ' - ' . $request->locality : '');
        $location->created_by = auth()->id();
        $location->save();

        return redirect()->route('location.index')->with('success', 'Location Added Successfully');
    }

    public function edit($id)
    {
        $location = Location::findOrFail($id);
        return view('location.edit', compact('location'));
    }

    public function update(LocationRequest $request, $id)
    {
        // Find the location or fail
        $location = Location::findOrFail($id);

        // Update city and locality
        $location->city = $request->city;
        $location->locality = $request->locality;

        // Update the name field as "City - Locality" (or just city if locality is empty)
        $location->name = $request->city . ($request->locality ? ' - ' . $request->locality : '');

        $location->save();

        return redirect()->route('location.index')->with('success', 'Location Updated Successfully');
    }


    public function destroy($id)
    {
        Location::where('id', $id)->delete();
        return redirect()->route('location.index')->with('success', 'Location Deleted Successfully');
    }

    public function ajaxCheckOrStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $name = $request->name;
        $parts = explode('-', $name, 2);
        $city = trim($parts[0]);
        $locality = isset($parts[1]) ? trim($parts[1]) : null;

        $location = Location::firstOrCreate(
            ['name' => $name],
            [
                'city' => $city,
                'locality' => $locality,
                'created_by' => auth()->id() ?? 1 // fallback if auth missing
            ]
        );

        return response()->json($location);
    }



    public function ajaxSearch(Request $request)
    {
        $term = $request->get('name');
        $locations = Location::where('name', 'LIKE', "%{$term}%")->get();

        return response()->json($locations);
    }


}

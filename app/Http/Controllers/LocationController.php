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
            $data = Location::all();
            return DataTables::of($data)
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('status', function ($row) {
                    return $row->status;
                })
                ->addColumn('created_at', function ($row) {
                    return date("d-m-Y", strtotime($row->created_at));
                })
                ->addColumn('updated_at', function ($row) {
                    return ($row->updated_at != "") ? date("d-m-Y", strtotime($row->updated_at)) : '-';
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->can('location-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('location.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('location-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteLocation('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('location.destroy', $row->id).'" method="POST" class="d-none">
                                        '.csrf_field().'
                                        '.method_field('delete').'
                                    </form>';
                    }
                    if (!empty($actions)) {
                        return '<div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                        '.$actions.'
                                        </div>
                                    </div>';
                    }
                    return '';
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
        //create location
        $location = new Location();
        $location->name = $request->input('name');
        $location->save();

        return redirect()->route('location.index')->with('success', 'Location Added Successfully');
    }

    public function edit($id)
    {
        $location = Location::find($id);
        return view('location.edit', compact('id', 'location'));
    }

    public function update(LocationRequest $request, $id)
    {
        $location = Location::find($id);
        $location->name = $request->input('name');
        $location->save();

        return redirect()->route('location.index')->with('success', 'Location Updated Successfully');
    }

    public function destroy($id)
    {
        Location::where('id', $id)->delete();
        return redirect()->route('location.index')->with('success', 'Location Deleted Successfully');
    }
}

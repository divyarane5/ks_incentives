<?php

namespace App\Http\Controllers;
use App\Models\BusinessUnit;
use App\Http\Requests\BusinessUnitRequest;
use DataTables;
use Illuminate\Http\Request;

class BusinessUnitController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:business_unit-view', ['only' => ['index']]);
        $this->middleware('permission:business_unit-create', ['only' => ['create','store']]);
        $this->middleware('permission:business_unit-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:business_unit-delete', ['only' => ['destroy']]);

    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BusinessUnit::latest();
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
                    return date("d-m-Y", strtotime($row->updated_at));
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->can('business_unit-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('business_unit.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('business_unit-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteBusinessUnit('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('business_unit.destroy', $row->id).'" method="POST" class="d-none">
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
        return view('business_unit.index');
    }

    public function create()
    {
        return view('business_unit.create');
    }

    public function store(BusinessUnitRequest $request)
    {
        //create location
        $businessUnit = new BusinessUnit();
        $businessUnit->name = $request->input('name');
        $businessUnit->save();

        return redirect()->route('business_unit.index')->with('success', 'Business Unit Added Successfully');
    }

    public function edit($id)
    {
        $businessUnit = BusinessUnit::find($id);
        return view('business_unit.edit', compact('id', 'businessUnit'));
    }

    public function update(BusinessUnitRequest $request, $id)
    {
        $businessUnit = BusinessUnit::find($id);
        $businessUnit->name = $request->input('name');
        $businessUnit->save();

        return redirect()->route('business_unit.index')->with('success', 'Business Unit Updated Successfully');
    }

    public function destroy($id)
    {
        BusinessUnit::where('id', $id)->delete();
        return redirect()->route('business_unit.index')->with('success', 'Business Unit Deleted Successfully');
    }
}

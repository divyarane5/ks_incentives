<?php

namespace App\Http\Controllers;
use App\Models\Designation;
use App\Http\Requests\DesignationRequest;
use DataTables;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:designation-view', ['only' => ['index']]);
        $this->middleware('permission:designation-create', ['only' => ['create','store']]);
        $this->middleware('permission:designation-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:designation-delete', ['only' => ['destroy']]);

    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->order[0]) && !empty($request->order[0])) {
                $orderColumn = $request->columns[$request->order[0]['column']]['name'];
            }
            $data = Designation::query();
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
                    if (auth()->user()->can('designation-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('designation.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('designation-delete')) {
                        $onclickAction = "event.preventDefault(); document.getElementById('".$row->id."').submit()";
                        $actions .= '<button class="dropdown-item" onclick="deleteDesignation('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('designation.destroy', $row->id).'" method="POST" class="d-none">
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
                ->orderColumn($orderColumn, $orderColumn.' $1')
                ->make(true);
        }
        return view('designation.index');
    }

    public function create()
    {
        return view('designation.create');
    }

    public function store(DesignationRequest $request)
    {
        //create designation
        $designation = new Designation();
        $designation->name = $request->input('name');
        $designation->save();

        return redirect()->route('designation.index')->with('success', 'Designation Added Successfully');
    }

    public function edit($id)
    {
        $designation = Designation::find($id);
        return view('designation.edit', compact('id', 'designation'));
    }

    public function update(DesignationRequest $request, $id)
    {
        $designation = Designation::find($id);
        $designation->name = $request->input('name');
        $designation->save();

        return redirect()->route('designation.index')->with('success', 'Designation Updated Successfully');
    }

    public function destroy($id)
    {
        Designation::where('id', $id)->delete();
        return redirect()->route('designation.index')->with('success', 'Designation Deleted Successfully');
    }
}

<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Http\Requests\DepartmentRequest;
use DataTables;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:department-view', ['only' => ['index']]);
        $this->middleware('permission:department-create', ['only' => ['create','store']]);
        $this->middleware('permission:department-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:department-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Department::query();
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
                    if (auth()->user()->can('department-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('department.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('department-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteDepartment('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('department.destroy', $row->id).'" method="POST" class="d-none">
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
        return view('department.index');
    }

    public function create()
    {
        return view('department.create');
    }

    public function store(DepartmentRequest $request)
    {
        //create department
        $department = new Department();
        $department->name = $request->input('name');
        $department->save();

        return redirect()->route('department.index')->with('success', 'Department Added Successfully');
    }

    public function edit($id)
    {
        $department = Department::find($id);
        return view('department.edit', compact('id', 'department'));
    }

    public function update(DepartmentRequest $request, $id)
    {
        $department = Department::find($id);
        $department->name = $request->input('name');
        $department->save();

        return redirect()->route('department.index')->with('success', 'Department Updated Successfully');
    }

    public function destroy($id)
    {
        Department::where('id', $id)->delete();
        return redirect()->route('department.index')->with('success', 'Department Deleted Successfully');
    }
}

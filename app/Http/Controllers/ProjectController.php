<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\ProjectRequest;
use DataTables;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:project-view', ['only' => ['index']]);
        $this->middleware('permission:project-create', ['only' => ['create','store']]);
        $this->middleware('permission:project-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:project-delete', ['only' => ['destroy']]);

    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Project::query();
            return DataTables::of($data)
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('brokerage', function ($row) {
                    return $row->brokerage." %";
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
                    if (auth()->user()->can('project-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('project.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('project-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteProject('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('project.destroy', $row->id).'" method="POST" class="d-none">
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
        return view('project.index');
    }

    public function create()
    {
        return view('project.create');
    }

    public function store(ProjectRequest $request)
    {
        //create project
        $project = new Project();
        $project->name = $request->input('name');
        $project->brokerage = $request->input('brokerage');
        $project->save();

        return redirect()->route('project.index')->with('success', 'Project Added Successfully');
    }

    public function edit($id)
    {
        $project = Project::find($id);
        return view('project.edit', compact('id', 'project'));
    }

    public function update(ProjectRequest $request, $id)
    {
        $project = Project::find($id);
        $project->name = $request->input('name');
        $project->brokerage = $request->input('brokerage');
        $project->save();

        return redirect()->route('project.index')->with('success', 'Project Updated Successfully');
    }

    public function destroy($id)
    {
        Project::where('id', $id)->delete();
        return redirect()->route('project.index')->with('success', 'Project Deleted Successfully');
    }
}

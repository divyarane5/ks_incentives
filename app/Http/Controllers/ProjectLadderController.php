<?php

namespace App\Http\Controllers;

use App\Models\ProjectLadder;
use App\Models\DeveloperLadder;
use App\Models\Project;
use App\Http\Requests\ProjectLadderRequest;
use App\Http\Requests\DeveloperLadderRequest;
use DataTables;
use Illuminate\Http\Request;
use DB;


class ProjectLadderController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:project_ladder-view', ['only' => ['index']]);
        $this->middleware('permission:project_ladder-create', ['only' => ['create','store']]);
        $this->middleware('permission:project_ladder-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:project_ladder-delete', ['only' => ['destroy']]);

    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProjectLadder::query();
            return DataTables::of($data)
                ->addColumn('project_id', function ($row) {
                    $project = DB::table('projects')
                            ->select('projects.name')
                            ->where('projects.id',$row->project_id)
                            ->first();
                    return $project->name;
                })
                ->addColumn('aop_id', function ($row) {
                    $developer_ladder = DeveloperLadder::select([ 'developer_ladders.id','name','aop','ladder','aop_s_date','aop_e_date'])
                    ->leftJoin('developers', 'developer_ladders.developer_id', '=', 'developers.id')->orderBy('developer_ladders.id', 'asc')
                    ->where('developer_ladders.id',$row->aop_id)->first();
                    return $developer_ladder->name." "
                    ."(".date('F', strtotime($developer_ladder->aop_s_date))." ".date('Y', strtotime($developer_ladder->aop_s_date))
                    ." - ".date('F', strtotime($developer_ladder->aop_e_date))." ".date('Y', strtotime($developer_ladder->aop_e_date)).")";
                })
                ->addColumn('booking', function ($row) {
                    return $row->booking;
                })
                ->addColumn('ladder', function ($row) {
                    return $row->ladder." %";
                })
                ->addColumn('project_s_date', function ($row) {
                    return date('F', strtotime($row->project_s_date))."-".date('Y', strtotime($row->project_s_date));
                })
                ->addColumn('project_e_date', function ($row) {
                    return date('F', strtotime($row->project_e_date))."-".date('Y', strtotime($row->project_e_date));
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
                    if (auth()->user()->can('project_ladder-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('project_ladder.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('project_ladder-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteProjectLadder('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('project_ladder.destroy', $row->id).'" method="POST" class="d-none">
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
        return view('project_ladder.index');
    }

    public function create()
    {
        $projects = Project::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $developer_ladders = DeveloperLadder::select([ 'developer_ladders.id','name','aop','ladder','aop_s_date','aop_e_date'])
        ->leftJoin('developers', 'developer_ladders.developer_id', '=', 'developers.id')->orderBy('developer_ladders.id', 'asc')->get();
       // print_r($developer_ladders); exit;
        return view('project_ladder.create', compact('projects','developer_ladders'));
       // return view('project_ladder.create');
    }

    public function store(ProjectLadderRequest $request)
    {
      //  print_r($_REQUEST); exit;
        //create project_ladder
        $project_ladder = new ProjectLadder();
        $project_ladder->project_id = $request->input('project_id');
        $project_ladder->aop_id = $request->input('aop_id');
        $project_ladder->booking = $request->input('booking');
        $project_ladder->ladder = $request->input('ladder');
        $project_ladder->project_s_date = $request->input('project_s_date');
        $project_ladder->project_e_date = $request->input('project_e_date');
        $project_ladder->save();

        return redirect()->route('project_ladder.index')->with('success', 'ProjectLadder Added Successfully');
    }

    public function edit($id)
    {
        $projects = Project::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $developer_ladders = DeveloperLadder::select([ 'developer_ladders.id','name','aop','ladder','aop_s_date','aop_e_date'])
        ->leftJoin('developers', 'developer_ladders.developer_id', '=', 'developers.id')->orderBy('developer_ladders.id', 'asc')->get();
     
        $project_ladder = ProjectLadder::find($id);
        return view('project_ladder.edit', compact('id', 'project_ladder','projects','developer_ladders'));
    }

    public function update(ProjectLadderRequest $request, $id)
    {
        $project_ladder = ProjectLadder::find($id);
        $project_ladder->project_id = $request->input('project_id');
        $project_ladder->aop_id = $request->input('aop_id');
        $project_ladder->booking = $request->input('booking');
        $project_ladder->ladder = $request->input('ladder');
        $project_ladder->project_s_date = $request->input('project_s_date');
        $project_ladder->project_e_date = $request->input('project_e_date');
        $project_ladder->save();

        return redirect()->route('project_ladder.index')->with('success', 'ProjectLadder Updated Successfully');
    }

    public function destroy($id)
    {
        ProjectLadder::where('id', $id)->delete();
        return redirect()->route('project_ladder.index')->with('success', 'ProjectLadder Deleted Successfully');
    }
}

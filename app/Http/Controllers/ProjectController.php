<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Project;
use App\Models\Developer;
use App\Models\ProjectLadder;
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

            $data = Project::with(['developer', 'ladders'])->select('projects.*');

            return DataTables::of($data)

                ->addColumn('developer', function ($row) {
                    return $row->developer->name ?? '-';
                })

                ->addColumn('ladders', function ($row) {

                    if ($row->ladders->isEmpty()) {
                        return '-';
                    }

                    $html = '';

                    // 🔹 BUTTON (ONLY ONCE)
                    $html .= '
                        <button class="btn btn-sm btn-warning mb-2" 
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#ladders-'.$row->id.'"
                            aria-expanded="false"
                            aria-controls="ladders-'.$row->id.'">
                            View Ladders
                        </button>
                    ';

                    // 🔹 COLLAPSE START (ONLY ONCE)
                    $html .= '<div class="collapse" id="ladders-'.$row->id.'">';
                    $html .= '<ul class="list-group">';

                    // 🔹 GROUP BY DATE
                    $grouped = $row->ladders->groupBy(function ($ladder) {
                        return $ladder->project_s_date->format('Y-m-d') . '_' .
                            $ladder->project_e_date->format('Y-m-d');
                    });

                    foreach ($grouped as $ladders) {

                        $first = $ladders->first();

                        $html .= '<li class="list-group-item">';
                        $html .= '<strong>Start:</strong> ' .
                                $first->project_s_date->format('d M Y') .
                                ' → <strong>End:</strong> ' .
                                $first->project_e_date->format('d M Y') . '<br>';

                        foreach ($ladders as $ladder) {
                            $html .= '
                                Min Deals: '.$ladder->s_booking.'
                                | Max Deals: '.$ladder->e_booking.'
                                | Ladder %: '.$ladder->ladder.'<br>
                            ';
                        }

                        $html .= '</li>';
                    }

                    $html .= '</ul>';
                    $html .= '</div>'; // collapse end

                    return $html;
                })
                 ->addColumn('action', function ($row) {
                    $actions = '';

                    if (auth()->user()->can('project-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('project.edit', $row->id).'">
                                        <i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('project-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteProject('.$row->id.')">
                                        <i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('project.destroy', $row->id).'" method="POST" class="d-none">
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
             
                ->rawColumns(['ladders','action'])
                ->make(true);
        }

        return view('project.index');
    }

    public function create()
    {
        $developers = Developer::all();

        return view('project.create', compact('developers'));
    }

    public function store(ProjectRequest $request)
    {
    //    echo "<pre>";
    //     print_r($request->all()); exit; 
        DB::transaction(function () use ($request) {

            // 1️⃣ Create Project
            $project = Project::create([
                'name'         => $request->name,
                'developer_id' => $request->developer_id,
            ]);

            // 2️⃣ Save Ladder Rows
            foreach ($request->ladders as $ladder) {

                // Skip empty rows (extra safety)
                if (!empty($ladder['s_booking']) && 
                    !empty($ladder['e_booking']) && 
                    !empty($ladder['ladder']) &&
                    !empty($ladder['project_s_date']) &&
                    !empty($ladder['project_e_date'])
                ) {

                    ProjectLadder::create([
                        'project_id'     => $project->id,
                        's_booking'      => $ladder['s_booking'],
                        'e_booking'      => $ladder['e_booking'],
                        'ladder'         => $ladder['ladder'],
                        'project_s_date' => $ladder['project_s_date'],
                        'project_e_date' => $ladder['project_e_date'],
                    ]);
                }
            }

        });

        return redirect()
            ->route('project.index')
            ->with('success', 'Project Added Successfully');
    }

    public function edit($id)
    {
         $developers = Developer::all();
        $project = Project::find($id);
        return view('project.edit', compact('id', 'project','developers'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        // Update project basic info
        $project->update([
            'name' => $request->name,
            'developer_id' => $request->developer_id,
        ]);

        // Delete old ladders
        $project->ladders()->delete();

        // Insert new ladders
        if ($request->ladders) {
            foreach ($request->ladders as $ladder) {
                $project->ladders()->create([
                    's_booking'      => $ladder['s_booking'],
                    'e_booking'      => $ladder['e_booking'],
                    'ladder'         => $ladder['ladder'],
                    'project_s_date' => $ladder['project_s_date'], // per ladder
                    'project_e_date' => $ladder['project_e_date'], // per ladder
                ]);
            }
        }

        return redirect()->route('project.index')
                        ->with('success','Project updated successfully.');
    }

    public function destroy($id)
    {
        Project::where('id', $id)->delete();
        return redirect()->route('project.index')->with('success', 'Project Deleted Successfully');
    }
}

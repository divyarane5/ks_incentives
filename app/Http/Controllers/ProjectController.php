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

                ->addColumn('base_brokerage_percent', function ($row) {
                    return $row->base_brokerage_percent
                        ? $row->base_brokerage_percent . '%'
                        : '0%';
                })

                ->addColumn('rera_number', function ($row) {
                    return $row->rera_number ?? '-';
                })

                ->addColumn('ladders', function ($row) {

                    if (!$row->ladders || $row->ladders->isEmpty()) {
                        return '-';
                    }

                    $html = '';

                    $html .= '
                        <button class="btn btn-sm btn-warning mb-2" 
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#ladders-'.$row->id.'">
                            View Ladders
                        </button>
                    ';

                    $html .= '<div class="collapse" id="ladders-'.$row->id.'">';
                    $html .= '<ul class="list-group">';

                    $grouped = $row->ladders->groupBy(function ($ladder) {
                        return optional($ladder->project_s_date)->format('Y-m-d') . '_' .
                               optional($ladder->project_e_date)->format('Y-m-d');
                    });

                    foreach ($grouped as $ladders) {

                        $first = $ladders->first();

                        $html .= '<li class="list-group-item">';

                        if ($first->project_s_date && $first->project_e_date) {
                            $html .= '<strong>Start:</strong> ' .
                                $first->project_s_date->format('d M Y') .
                                ' → <strong>End:</strong> ' .
                                $first->project_e_date->format('d M Y') . '<br>';
                        }

                        foreach ($ladders as $ladder) {
                            $html .= '
                                Min Deals: '.($ladder->s_booking ?? '-') .'
                                | Max Deals: '.($ladder->e_booking ?? '-') .'
                                | Ladder %: '.($ladder->ladder ?? '-') .'<br>
                            ';
                        }

                        $html .= '</li>';
                    }

                    $html .= '</ul></div>';

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
                                    <form id="delete-'.$row->id.'" action="'.route('project.destroy', $row->id).'" method="POST" class="d-none">
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
        DB::transaction(function () use ($request) {

            // ✅ Create Project
            $project = Project::create([
                'name'                   => $request->name,
                'developer_id'           => $request->developer_id,
                'base_brokerage_percent' => $request->base_brokerage_percent ?? 0,
                'rera_number'            => $request->rera_number,
            ]);

            // ✅ Ladders OPTIONAL
            if (!empty($request->ladders)) {
                foreach ($request->ladders as $ladder) {

                    if (
                        !empty($ladder['s_booking']) ||
                        !empty($ladder['e_booking']) ||
                        !empty($ladder['ladder']) ||
                        !empty($ladder['project_s_date']) ||
                        !empty($ladder['project_e_date'])
                    ) {
                        ProjectLadder::create([
                            'project_id'     => $project->id,
                            's_booking'      => $ladder['s_booking'] ?? null,
                            'e_booking'      => $ladder['e_booking'] ?? null,
                            'ladder'         => $ladder['ladder'] ?? null,
                            'project_s_date' => $ladder['project_s_date'] ?? null,
                            'project_e_date' => $ladder['project_e_date'] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('project.index')
            ->with('success', 'Project Added Successfully');
    }

    public function edit($id)
    {
        $developers = Developer::all();
        $project = Project::findOrFail($id);

        return view('project.edit', compact('project','developers'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        // ✅ Update Project
        $project->update([
            'name'                   => $request->name,
            'developer_id'           => $request->developer_id,
            'base_brokerage_percent' => $request->base_brokerage_percent ?? 0,
            'rera_number'            => $request->rera_number,
        ]);

        // ✅ Refresh ladders (optional)
        $project->ladders()->delete();

        if (!empty($request->ladders)) {
            foreach ($request->ladders as $ladder) {

                if (
                    !empty($ladder['s_booking']) ||
                    !empty($ladder['e_booking']) ||
                    !empty($ladder['ladder']) ||
                    !empty($ladder['project_s_date']) ||
                    !empty($ladder['project_e_date'])
                ) {
                    $project->ladders()->create([
                        's_booking'      => $ladder['s_booking'] ?? null,
                        'e_booking'      => $ladder['e_booking'] ?? null,
                        'ladder'         => $ladder['ladder'] ?? null,
                        'project_s_date' => $ladder['project_s_date'] ?? null,
                        'project_e_date' => $ladder['project_e_date'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('project.index')
            ->with('success','Project updated successfully.');
    }

    public function destroy($id)
    {
        Project::where('id', $id)->delete();

        return redirect()->route('project.index')
            ->with('success', 'Project Deleted Successfully');
    }
}
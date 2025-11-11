<?php

namespace App\Http\Controllers;

use App\Models\BusinessUnit;
use App\Models\MandateProject;
use App\Models\MandateProjectConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

class MandateProjectController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MandateProject::with('configurations');

            return DataTables::of($data)
                ->addColumn('project_name', fn($row) => $row->project_name)
                ->addColumn('brand_name', fn($row) => $row->brand_name)
                ->addColumn('location', fn($row) => $row->location)
                ->addColumn('rera_number', fn($row) => $row->rera_number)
                ->addColumn('property_type', fn($row) => ucfirst($row->property_type))
                ->addColumn('configurations', function ($row) {
                    $html = '';
                    if ($row->configurations->count()) {
                        $html .= '<button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#configs-'.$row->id.'">
                                    View Configurations
                                </button>
                                <div class="collapse mt-2" id="configs-'.$row->id.'">
                                    <ul class="list-group">';
                        foreach ($row->configurations as $config) {
                            $html .= '<li class="list-group-item">'.e($config->config).' - '.e($config->carpet_area).' sqft</li>';
                        }
                        $html .= '</ul></div>';
                    }
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $actions = '';

                    if (auth()->user()->can('mandate_projects-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('mandate_projects.edit', $row->id).'">
                                        <i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('mandate_projects-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteMandateProject('.$row->id.')">
                                        <i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('mandate_projects.destroy', $row->id).'" method="POST" class="d-none">
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
                ->rawColumns(['configurations', 'action'])
                ->make(true);
        }

        return view('mandate_projects.index');
    }



    public function create()
    {
        return view('mandate_projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'property_type' => 'required|in:residential,commercial',
            'rera_number' => 'nullable|string|max:255',
            'configurations' => 'nullable|array',
            'configurations.*' => 'required|string|max:255',
            'carpet_areas' => 'nullable|array',
            'carpet_areas.*' => 'nullable|numeric|min:0',
        ]);

        $alterra = BusinessUnit::where('name', 'Alterra India')->first();
        if (!$alterra) {
            return redirect()->back()->with('error', 'Business Unit Alterra India not found.');
        }

        DB::transaction(function () use ($request, $alterra) {
            $project = MandateProject::create([
                'project_name' => $request->project_name,
                'brand_name' => $request->brand_name,
                'location' => $request->location,
                'property_type' => $request->property_type,
                'rera_number' => $request->rera_number,
                'business_unit_id' => $alterra->id,
            ]);

            if ($request->has('configurations')) {
                foreach ($request->configurations as $i => $config) {
                    MandateProjectConfiguration::create([
                        'mandate_project_id' => $project->id,
                        'config' => $config,
                        'carpet_area' => $request->carpet_areas[$i] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('mandate_projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(MandateProject $mandateProject)
    {
        $mandateProject->load('configurations');
        return view('mandate_projects.show', compact('mandateProject'));
    }

    public function edit(MandateProject $mandateProject)
    {
        $mandateProject->load('configurations');
        return view('mandate_projects.edit', compact('mandateProject'));
    }

    public function update(Request $request, MandateProject $mandateProject)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'property_type' => 'required|in:residential,commercial',
            'rera_number' => 'nullable|string|max:255',
            'configurations' => 'nullable|array',
            'configurations.*' => 'required|string|max:255',
            'carpet_areas' => 'nullable|array',
            'carpet_areas.*' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $mandateProject) {
            $mandateProject->update($request->only([
                'project_name',
                'brand_name',
                'location',
                'property_type',
                'rera_number',
            ]));

            // Refresh configurations
            $mandateProject->configurations()->delete();

            if ($request->has('configurations')) {
                foreach ($request->configurations as $i => $config) {
                    MandateProjectConfiguration::create([
                        'mandate_project_id' => $mandateProject->id,
                        'config' => $config,
                        'carpet_area' => $request->carpet_areas[$i] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('mandate_projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(MandateProject $mandateProject)
    {
        $mandateProject->delete();

        return redirect()->route('mandate_projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}

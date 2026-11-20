<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Projects are the same list as in the back office (bookings, brokerage). They are created
 * there with their developer and brokerage; here admins only add the details sales needs.
 */
class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::with('developer')->withCount('leads')
            ->when($request->query('q'), fn ($q, $v) => $q->where(fn ($w) => $w->where('name', 'like', "%{$v}%")->orWhere('location', 'like', "%{$v}%")))
            ->when($request->query('category'), fn ($q, $v) => $q->where('category', $v))
            ->orderByDesc('status')->orderBy('name')
            ->paginate(25)->withQueryString();

        return view('crm.projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        $project->load(['developer', 'properties.type'])->loadCount('leads');

        return view('crm.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('crm.projects.form', ['project' => $project]);
    }

    public function update(Request $request, Project $project)
    {
        $project->update($request->validate([
            'category' => ['required', Rule::in(['residential', 'commercial', 'agricultural'])],
            'launch_status' => ['nullable', Rule::in(['upcoming', 'pre_launch', 'launch', 'ongoing', 'ready_to_move', 'resale'])],
            'location' => ['nullable', 'string', 'max:200'],
            'city' => ['nullable', 'string', 'max:100'],
            'min_price' => ['nullable', 'integer', 'min:0'],
            'max_price' => ['nullable', 'integer', 'min:0', 'gte:min_price'],
            'possession_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]));

        return redirect()->route('crm.projects.show', $project)->with('status', 'Project updated.');
    }
}

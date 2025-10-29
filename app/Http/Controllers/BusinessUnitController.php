<?php

namespace App\Http\Controllers;

use App\Models\BusinessUnit;
use App\Http\Requests\BusinessUnitRequest;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            $data = BusinessUnit::query();
            return DataTables::of($data)
                ->addColumn('name', fn($row) => $row->name)
                ->addColumn('status', fn($row) => $row->status == 1 ? 'Active' : 'Inactive')
                ->addColumn('created_at', fn($row) => date("d-m-Y", strtotime($row->created_at)))
                ->addColumn('updated_at', fn($row) => date("d-m-Y", strtotime($row->updated_at)))
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->can('business_unit-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('business_unit.edit', $row->id).'"><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }
                    if (auth()->user()->can('business_unit-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteBusinessUnit('.$row->id.')"><i class="bx bx-trash me-1"></i> Delete</button>
                                     <form id="'.$row->id.'" action="'.route('business_unit.destroy', $row->id).'" method="POST" class="d-none">'
                                     .csrf_field().method_field('delete').
                                     '</form>';
                    }
                    return !empty($actions) ? '<div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">'.$actions.'</div>
                                                </div>' : '';
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
        $businessUnit = new BusinessUnit();
        $businessUnit->name = $request->name;
        $businessUnit->code = $request->code;
        $businessUnit->domain = $request->domain;
        $businessUnit->theme_color = $request->theme_color ?? '#1e40af';
        $businessUnit->secondary_color = $request->secondary_color ?? '#f0f4f8';
        $businessUnit->status = $request->status ?? 1;

        // Handle file uploads
        if ($request->hasFile('logo')) {
            $businessUnit->logo_path = $request->file('logo')->store('uploads/logos', 'public');
        }
        if ($request->hasFile('background')) {
            $businessUnit->background_path = $request->file('background')->store('uploads/backgrounds', 'public');
        }
        if ($request->hasFile('favicon')) {
            $businessUnit->favicon_path = $request->file('favicon')->store('uploads/favicons', 'public');
        }

        $businessUnit->save();

        return redirect()->route('business_unit.index')->with('success', 'Business Unit Added Successfully');
    }

    public function edit($id)
    {
        $businessUnit = BusinessUnit::findOrFail($id);
        return view('business_unit.edit', compact('businessUnit'));
    }

    public function update(BusinessUnitRequest $request, $id)
    {
        $businessUnit = BusinessUnit::findOrFail($id);
        $businessUnit->name = $request->name;
        $businessUnit->code = $request->code;
        $businessUnit->domain = $request->domain;
        $businessUnit->theme_color = $request->theme_color ?? '#1e40af';
        $businessUnit->secondary_color = $request->secondary_color ?? '#f0f4f8';
        $businessUnit->status = $request->status ?? 1;

        // Handle file uploads and replace old files if exist
        if ($request->hasFile('logo')) {
            if ($businessUnit->logo_path) Storage::disk('public')->delete($businessUnit->logo_path);
            $businessUnit->logo_path = $request->file('logo')->store('uploads/logos', 'public');
        }
        if ($request->hasFile('background')) {
            if ($businessUnit->background_path) Storage::disk('public')->delete($businessUnit->background_path);
            $businessUnit->background_path = $request->file('background')->store('uploads/backgrounds', 'public');
        }
        if ($request->hasFile('favicon')) {
            if ($businessUnit->favicon_path) Storage::disk('public')->delete($businessUnit->favicon_path);
            $businessUnit->favicon_path = $request->file('favicon')->store('uploads/favicons', 'public');
        }

        $businessUnit->save();

        return redirect()->route('business_unit.index')->with('success', 'Business Unit Updated Successfully');
    }

    public function destroy($id)
    {
        $businessUnit = BusinessUnit::findOrFail($id);

        // Delete files if exist
        if ($businessUnit->logo_path) Storage::disk('public')->delete($businessUnit->logo_path);
        if ($businessUnit->background_path) Storage::disk('public')->delete($businessUnit->background_path);
        if ($businessUnit->favicon_path) Storage::disk('public')->delete($businessUnit->favicon_path);

        $businessUnit->delete();

        return redirect()->route('business_unit.index')->with('success', 'Business Unit Deleted Successfully');
    }
}

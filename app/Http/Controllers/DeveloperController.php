<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use App\Http\Requests\DeveloperRequest;
use DataTables;
use Illuminate\Http\Request;

class DeveloperController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:developer-view', ['only' => ['index']]);
        $this->middleware('permission:developer-create', ['only' => ['create','store']]);
        $this->middleware('permission:developer-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:developer-delete', ['only' => ['destroy']]);

    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Developer::query();
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
                    if (auth()->user()->can('developer-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('developer.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('developer-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteDeveloper('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('developer.destroy', $row->id).'" method="POST" class="d-none">
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
        return view('developer.index');
    }

    public function create()
    {
        return view('developer.create');
    }

    public function store(DeveloperRequest $request)
    {
        //create developer
        $developer = new Developer();
        $developer->name = $request->input('name');
        $developer->save();

        return redirect()->route('developer.index')->with('success', 'Developer Added Successfully');
    }

    public function edit($id)
    {
        $developer = Developer::find($id);
        return view('developer.edit', compact('id', 'developer'));
    }

    public function update(DeveloperRequest $request, $id)
    {
        $developer = Developer::find($id);
        $developer->name = $request->input('name');
        $developer->save();

        return redirect()->route('developer.index')->with('success', 'Developer Updated Successfully');
    }

    public function destroy($id)
    {
        Developer::where('id', $id)->delete();
        return redirect()->route('developer.index')->with('success', 'Developer Deleted Successfully');
    }
}

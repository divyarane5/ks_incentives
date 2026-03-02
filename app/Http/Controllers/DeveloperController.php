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
                ->addColumn('view_ladders', function ($row) {

                    if ($row->ladders->isEmpty()) {
                        return '-';
                    }

                    $html = '
                        <button class="btn btn-sm btn-warning" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#ladders-'.$row->id.'">
                            View Ladders
                        </button>

                        <div class="collapse mt-2" id="ladders-'.$row->id.'">
                            <ul class="list-group">';

                    foreach ($row->ladders as $ladder) {

                        $html .= '
                            <li class="list-group-item">
                                <strong>'.date('d M Y', strtotime($ladder->aop_s_date)).'</strong>
                                →
                                <strong>'.date('d M Y', strtotime($ladder->aop_e_date)).'</strong>
                                <br>
                                AOP: '.$ladder->min_aop.' Cr - '.$ladder->max_aop.' Cr
                                | Brokerage: '.$ladder->ladder.'%
                            </li>';
                    }

                    $html .= '
                            </ul>
                        </div>';

                    return $html;
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
                ->rawColumns(['action','view_ladders'])
                ->make(true);
        }
        return view('developer.index');
    }

    public function create()
    {
        return view('developer.create');
    }

    public function store(Request $request)
    {
        // echo "<pre>"; 
        // print_r($request->all()); exit;
        $request->validate([
            'name' => 'required|string|max:255',

            'min_aop.*' => 'required|numeric',
            'max_aop.*' => 'required|numeric|gte:min_aop.*',
            'ladder.*'  => 'required|numeric',

            'aop_s_date.*' => 'required|date',
            'aop_e_date.*' => 'required|date|after_or_equal:aop_s_date.*',
        ]);

        $developer = Developer::create([
            'name' => $request->name,
        ]);

        foreach ($request->min_aop as $key => $minAop) {

            $developer->ladders()->create([
                'aop' => $minAop." - ".$request->max_aop[$key],
                'min_aop'    => $minAop,
                'max_aop'    => $request->max_aop[$key],
                'ladder'     => $request->ladder[$key],
                'aop_s_date' => $request->aop_s_date[$key],
                'aop_e_date' => $request->aop_e_date[$key],
                'ladder_type'=> 'flat', // default (if needed)
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->route('developer.index')
            ->with('success', 'Developer created successfully.');
    }
    
    public function edit($id)
    {
        $developer = Developer::with('ladders')->findOrFail($id);

        return view('developer.edit', compact('developer'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',

            'min_aop.*' => 'required|numeric',
            'max_aop.*' => 'required|numeric',
            'ladder.*'  => 'required|numeric',

            'aop_s_date.*' => 'required|date',
            'aop_e_date.*' => 'required|date',
        ]);

        $developer = Developer::findOrFail($id);

        $developer->update([
            'name' => $request->name,
        ]);

        // 🔥 Delete old ladders
        $developer->ladders()->delete();

        // 🔥 Recreate ladders
        foreach ($request->min_aop as $key => $minAop) {

            $developer->ladders()->create([
                'aop'        => $minAop.' - '.$request->max_aop[$key],
                'min_aop'    => $minAop,
                'max_aop'    => $request->max_aop[$key],
                'ladder'     => $request->ladder[$key],
                'aop_s_date' => $request->aop_s_date[$key],
                'aop_e_date' => $request->aop_e_date[$key],
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->route('developer.index')
            ->with('success', 'Developer updated successfully.');
    }
    public function destroy($id)
    {
        Developer::where('id', $id)->delete();
        return redirect()->route('developer.index')->with('success', 'Developer Deleted Successfully');
    }
}

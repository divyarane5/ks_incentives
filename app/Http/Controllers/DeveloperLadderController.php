<?php

namespace App\Http\Controllers;
use App\Models\DeveloperLadder;
use App\Models\Developer;
use App\Http\Requests\DeveloperLadderRequest;
use DataTables;
use Illuminate\Http\Request;
use DB;

class DeveloperLadderController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:developer_ladder-view', ['only' => ['index']]);
        $this->middleware('permission:developer_ladder-create', ['only' => ['create','store']]);
        $this->middleware('permission:developer_ladder-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:developer_ladder-delete', ['only' => ['destroy']]);

    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DeveloperLadder::query();
            return DataTables::of($data)
                ->addColumn('developer_id', function ($row) {
                    $developer = DB::table('developers')
                            ->select('developers.name')
                            ->where('developers.id',$row->developer_id)
                            ->first();
                    return $developer->name;
                })
                ->addColumn('aop', function ($row) {
                    return "Rs. ".$row->aop;
                })
                ->addColumn('ladder', function ($row) {
                    return $row->ladder." %";
                })
                ->addColumn('aop_s_date', function ($row) {
                    return date('F', strtotime($row->aop_s_date))."-".date('Y', strtotime($row->aop_s_date));
                })
                ->addColumn('aop_e_date', function ($row) {
                    return date('F', strtotime($row->aop_e_date))."-".date('Y', strtotime($row->aop_e_date));
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
                    if (auth()->user()->can('developer_ladder-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('developer_ladder.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('developer_ladder-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteDeveloperLadder('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('developer_ladder.destroy', $row->id).'" method="POST" class="d-none">
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
        return view('developer_ladder.index');
    }

    public function create()
    {
        $developers = Developer::select(['id', 'name'])->orderBy('name', 'asc')->get();
        return view('developer_ladder.create', compact('developers'));
       // return view('developer_ladder.create');
    }

    public function store(DeveloperLadderRequest $request)
    {
        //create developer_ladder
        $developer_ladder = new DeveloperLadder();
        $developer_ladder->developer_id = $request->input('developer_id');
        $developer_ladder->aop = $request->input('aop');
        $developer_ladder->ladder = $request->input('ladder');
        $developer_ladder->aop_s_date = $request->input('aop_s_date');
        $developer_ladder->aop_e_date = $request->input('aop_e_date');
        $developer_ladder->save();

        return redirect()->route('developer_ladder.index')->with('success', 'DeveloperLadder Added Successfully');
    }

    public function edit($id)
    {
        $developers = Developer::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $developer_ladder = DeveloperLadder::find($id);
        return view('developer_ladder.edit', compact('id', 'developer_ladder','developers'));
    }

    public function update(DeveloperLadderRequest $request, $id)
    {
        $developer_ladder = DeveloperLadder::find($id);
        $developer_ladder->developer_id = $request->input('developer_id');
        $developer_ladder->aop = $request->input('aop');
        $developer_ladder->ladder = $request->input('ladder');
        $developer_ladder->aop_s_date = $request->input('aop_s_date');
        $developer_ladder->aop_e_date = $request->input('aop_e_date');
        $developer_ladder->save();

        return redirect()->route('developer_ladder.index')->with('success', 'DeveloperLadder Updated Successfully');
    }

    public function destroy($id)
    {
        DeveloperLadder::where('id', $id)->delete();
        return redirect()->route('developer_ladder.index')->with('success', 'DeveloperLadder Deleted Successfully');
    }
}

<?php

namespace App\Http\Controllers;
use App\Models\Vendor;
use App\Http\Requests\VendorRequest;
use DataTables;
use Illuminate\Http\Request;
use Auth;

class VendorController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:vendor-view', ['only' => ['index']]);
        $this->middleware('permission:vendor-create', ['only' => ['create','store']]);
        $this->middleware('permission:vendor-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:vendor-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Vendor::all();
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
                    return date("d-m-Y", strtotime($row->updated_at));
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->can('vendor-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('vendor.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('vendor-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteVendor('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('vendor.destroy', $row->id).'" method="POST" class="d-none">
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
        return view('vendor.index');
    }

    public function create()
    {
        return view('vendor.create');
    }

    public function store(VendorRequest $request)
    {
        //create location
        $vendor = new Vendor();
        $vendor->name = $request->input('name');
        $vendor->save();

        return redirect()->route('vendor.index')->with('success', 'Vendor Added Successfully');
    }

    public function edit($id)
    {
        $vendor = Vendor::find($id);
        return view('vendor.edit', compact('id', 'vendor'));
    }

    public function update(VendorRequest $request, $id)
    {
        $vendor = Vendor::find($id);
        $vendor->name = $request->input('name');
        $vendor->save();

        return redirect()->route('vendor.index')->with('success', 'Vendor Updated Successfully');
    }

    public function destroy($id)
    {
        Vendor::where('id', $id)->delete();
        return redirect()->route('vendor.index')->with('success', 'Vendor Deleted Successfully');
    }

}

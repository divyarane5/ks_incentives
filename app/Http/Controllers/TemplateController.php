<?php
namespace App\Http\Controllers;

use App\Models\Template;
use App\Http\Requests\TemplateRequest;
use DataTables;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:referral-template-view', ['only' => ['index']]);
        $this->middleware('permission:referral-template-create', ['only' => ['create','store']]);
        $this->middleware('permission:referral-template-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:referral-template-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Template::query();
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
                    $actions .= '<a class="dropdown-item" href="'.route('template.show', $row->id).'"
                                        ><i class="bx bx-show me-1"></i> View</a>';

                    if (auth()->user()->can('referral-template-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('template.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('referral-template-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteTemplate('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('template.destroy', $row->id).'" method="POST" class="d-none">
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
        return view('template.index');
    }

    public function create()
    {
        return view('template.create');
    }

    public function store(TemplateRequest $request)
    {

        //create location
        $template = new Template();
        $template->content = $request->input('content');
        $template->name = $request->input('name');
        $template->save();

        return redirect()->route('template.index')->with('success', 'Template Added Successfully');
    }

    public function edit($id)
    {
        $template = Template::find($id);
        return view('template.edit', compact('id', 'template'));
    }

    public function update(TemplateRequest $request, $id)
    {
        $template = Template::find($id);
        $template->name = $request->input('name');
        $template->content = $request->input('content');
        $template->save();

        return redirect()->route('template.index')->with('success', 'Template Updated Successfully');
    }

    public function destroy($id)
    {
        Template::where('id', $id)->delete();
        return redirect()->route('template.index')->with('success', 'Template Deleted Successfully');
    }

    public function show($id)
    {
        $template = Template::find($id);
        return view('template.show', compact('id', 'template'));
    }
}

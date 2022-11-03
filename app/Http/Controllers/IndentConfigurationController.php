<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndentConfigurationRequest;
use App\Interfaces\IndentConfigurationRepositoryInterface;
use App\Models\Expense;
use App\Models\IndentConfiguration;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;

class IndentConfigurationController extends Controller
{
    private $indentConfigurationRepository;

    function __construct(IndentConfigurationRepositoryInterface $indentConfigurationRepository)
    {
        $this->middleware('permission:configuration-view', ['only' => ['index']]);
        $this->middleware('permission:configuration-create', ['only' => ['create','store']]);
        $this->middleware('permission:configuration-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:configuration-delete', ['only' => ['destroy']]);

        $this->indentConfigurationRepository = $indentConfigurationRepository;
    }

    public function index(Request $request)
    {
        $userId = $request->has('user_id') ? $request->get('user_id') : '';
        if ($request->ajax()) {
            $indentConfigurations = $this->indentConfigurationRepository->getIndentConfigurations($userId);
            return DataTables::of($indentConfigurations)
                ->addColumn('created_at', function ($row) {
                    return date("d-m-Y", strtotime($row->created_at));
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->can('user-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('indent_configuration.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('user-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteIndentConfiguration('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('indent_configuration.destroy', $row->id).'" method="POST" class="d-none">
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
        return view('indent_configuration.index', compact('userId'));
    }

    public function create(Request $request)
    {
        $userId = $request->has('user_id') ? $request->get('user_id') : '';
        $users = User::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $expenses = Expense::select(['id', 'name'])->orderBy('name', 'asc')->get();
        return view('indent_configuration.create', compact('users', 'expenses', 'userId'));
    }

    public function store(IndentConfigurationRequest $request)
    {
        $indentConfiguration = new IndentConfiguration();
        $indentConfiguration->user_id = $request->input('user_id');
        $indentConfiguration->expense_id = $request->input('expense_id');
        $indentConfiguration->approver1 = $request->input('approver1');
        $indentConfiguration->approver2 = $request->input('approver2');
        $indentConfiguration->approver3 = $request->input('approver3');
        $indentConfiguration->approver4 = $request->input('approver4');
        $indentConfiguration->approver5 = $request->input('approver5');
        $indentConfiguration->monthly_limit = $request->input('monthly_limit');
        $indentConfiguration->indent_limit = $request->input('indent_limit');
        $indentConfiguration->monthly_limit_approval_required = ($request->has('monthly_limit_approval_required') ? 1 : 0);
        $indentConfiguration->indent_limit_approval_required = ($request->has('indent_limit_approval_required') ? 1 : 0);
        $indentConfiguration->save();

        return redirect()->route('indent_configuration.index')->with('success', 'Indent Configuration Added Successfully');
    }

    public function edit($id)
    {
        $users = User::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $expenses = Expense::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $intendConfiguration = IndentConfiguration::find($id);
        return view('indent_configuration.edit', compact('users', 'expenses', 'intendConfiguration', 'id'));
    }

    public function update(IndentConfigurationRequest $request, $id)
    {
        $indentConfiguration = IndentConfiguration::find($id);
        $indentConfiguration->user_id = $request->input('user_id');
        $indentConfiguration->expense_id = $request->input('expense_id');
        $indentConfiguration->approver1 = $request->input('approver1');
        $indentConfiguration->approver2 = $request->input('approver2');
        $indentConfiguration->approver3 = $request->input('approver3');
        $indentConfiguration->approver4 = $request->input('approver4');
        $indentConfiguration->approver5 = $request->input('approver5');
        $indentConfiguration->monthly_limit = $request->input('monthly_limit');
        $indentConfiguration->indent_limit = $request->input('indent_limit');
        $indentConfiguration->monthly_limit_approval_required = ($request->has('monthly_limit_approval_required') ? 1 : 0);
        $indentConfiguration->indent_limit_approval_required = ($request->has('indent_limit_approval_required') ? 1 : 0);
        $indentConfiguration->save();

        return redirect()->route('indent_configuration.index')->with('success', 'Indent Configuration Updated Successfully');
    }

    public function destroy($id)
    {
        IndentConfiguration::where('id', $id)->delete();
        return redirect()->route('indent_configuration.index')->with('success', 'Indent Configuration Deleted Successfully');
    }
}

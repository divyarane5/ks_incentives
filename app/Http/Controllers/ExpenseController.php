<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:expense-view', ['only' => ['index']]);
        $this->middleware('permission:expense-create', ['only' => ['create','store']]);
        $this->middleware('permission:expense-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:expense-delete', ['only' => ['destroy']]);

    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Expense::latest();
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
                    if (auth()->user()->can('expense-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('expense.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('expense-delete')) {
                        $onclickAction = "event.preventDefault(); document.getElementById('".$row->id."').submit()";
                        $actions .= '<button class="dropdown-item" onclick="'.$onclickAction.'"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('expense.destroy', $row->id).'" method="POST" class="d-none">
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
        return view('location.index');
    }

    public function create()
    {
        return view('expense.create');
    }

    public function store(LocationRequest $request)
    {
        //create location
        $expense = new Expense();
        $expense->name = $request->input('name');
        $expense->save();

        return redirect()->route('expense.index')->with('success', 'Expense Added Successfully');
    }

    public function edit($id)
    {
        $expense = Expense::find($id);
        return view('expense.edit', compact('id', 'expense'));
    }

    public function update(LocationRequest $request, $id)
    {
        $expense = Expense::find($id);
        $expense->name = $request->input('name');
        $expense->save();

        return redirect()->route('expense.index')->with('success', 'Expense Updated Successfully');
    }

    public function destroy($id)
    {
        Expense::where('id', $id)->delete();
        return redirect()->route('expense.index')->with('success', 'Expense Deleted Successfully');
    }
}

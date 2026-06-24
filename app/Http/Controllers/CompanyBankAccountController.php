<?php

namespace App\Http\Controllers;

use App\Models\CompanyBankAccount;
use Illuminate\Http\Request;
use DataTables;

class CompanyBankAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:company-bank-view', ['only' => ['index']]);
        $this->middleware('permission:company-bank-create', ['only' => ['create','store']]);
        $this->middleware('permission:company-bank-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:company-bank-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = CompanyBankAccount::orderByDesc('id');

            return DataTables::of($data)

                ->addColumn('status', function ($row) {
                    return $row->status ? 'Active' : 'Inactive';
                })

                ->addColumn('action', function ($row) {

                    $actions = '';

                    if (auth()->user()->can('company-bank-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('company-bank.edit',$row->id).'">Edit</a>';
                    }

                    if (auth()->user()->can('company-bank-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteRow('.$row->id.')">Delete</button>
                        <form id="'.$row->id.'" action="'.route('company-bank.destroy',$row->id).'" method="POST">
                            '.csrf_field().method_field('DELETE').'
                        </form>';
                    }

                    return '<div class="dropdown">
                                <button class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown">Action</button>
                                <div class="dropdown-menu">'.$actions.'</div>
                            </div>';
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('company_bank.index');
    }

    public function create()
    {
        return view('company_bank.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required',
            'bank_name' => 'nullable',
            'account_number' => 'nullable',
            'ifsc' => 'nullable',
            'gstin' => 'nullable',
        ]);

        CompanyBankAccount::create($request->all());

        return redirect()->route('company-bank.index')
            ->with('success','Created successfully');
    }

    public function edit($id)
    {
        $account = CompanyBankAccount::findOrFail($id);
        return view('company_bank.edit', compact('account'));
    }

    public function update(Request $request, $id)
    {
        $account = CompanyBankAccount::findOrFail($id);

        $account->update($request->all());

        return redirect()->route('company-bank.index')
            ->with('success','Updated successfully');
    }

    public function destroy($id)
    {
        CompanyBankAccount::where('id',$id)->delete();

        return redirect()->route('company-bank.index')
            ->with('success','Deleted successfully');
    }
}
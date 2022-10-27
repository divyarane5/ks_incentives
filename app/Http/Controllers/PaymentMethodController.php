<?php

namespace App\Http\Controllers;
use App\Models\PaymentMethod;
use App\Http\Requests\PaymentMethodRequest;
use DataTables;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:payment_method-view', ['only' => ['index']]);
        $this->middleware('permission:payment_method-create', ['only' => ['create','store']]);
        $this->middleware('permission:payment_method-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:payment_method-delete', ['only' => ['destroy']]);

    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PaymentMethod::all();
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
                    if (auth()->user()->can('payment_method-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('payment_method.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('payment_method-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deletePaymentMethod('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('payment_method.destroy', $row->id).'" method="POST" class="d-none">
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
        return view('payment_method.index');
    }

    public function create()
    {
        return view('payment_method.create');
    }

    public function store(PaymentMethodRequest $request)
    {
        //create location
        $paymentMethod = new PaymentMethod();
        $paymentMethod->name = $request->input('name');
        $paymentMethod->save();

        return redirect()->route('payment_method.index')->with('success', 'Payment Method Added Successfully');
    }

    public function edit($id)
    {
        $paymentMethod = PaymentMethod::find($id);
        return view('payment_method.edit', compact('id', 'paymentMethod'));
    }

    public function update(PaymentMethodRequest $request, $id)
    {
        $paymentMethod = PaymentMethod::find($id);
        $paymentMethod->name = $request->input('name');
        $paymentMethod->save();

        return redirect()->route('payment_method.index')->with('success', 'Payment Method Updated Successfully');
    }

    public function destroy($id)
    {
        PaymentMethod::where('id', $id)->delete();
        return redirect()->route('payment_method.index')->with('success', 'Payment Method Deleted Successfully');
    }
}

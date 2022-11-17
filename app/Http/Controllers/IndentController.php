<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndentRequest;
use App\Interfaces\IndentRepositoryInterface;
use App\Models\BusinessUnit;
use App\Models\Expense;
use App\Models\Indent;
use App\Models\IndentAttachment;
use App\Models\IndentComment;
use App\Models\IndentItem;
use App\Models\IndentPayment;
use App\Models\Location;
use App\Models\PaymentMethod;
use Exception;
use Illuminate\Http\Request;
use DB;
use DataTables;

class IndentController extends Controller
{
    private $indentRepository;

    function __construct(IndentRepositoryInterface $indentRepository)
    {
        $this->middleware('permission:indent-view-own|indent-view-all', ['only' => ['index']]);
        $this->middleware('permission:indent-approval', ['only' => ['indentApproval']]);
        $this->middleware('permission:indent-create', ['only' => ['create','store']]);
        $this->middleware('permission:indent-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:indent-delete', ['only' => ['destroy']]);
        $this->middleware('permission:indent-payment-conclude', ['only' => ['indentClosure', 'updatePayment']]);

        $this->indentRepository = $indentRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $indentRequest = $request->only(['location_id', 'bill_mode', 'business_unit_id', 'status']);
            $indents = Indent::select(['indents.id', 'indents.title', 'locations.name as location', 'business_units.name as business_unit', 'bill_mode', 'indents.total', 'indents.status', 'indents.created_at', 'users.name as raised_by'])
                        ->join('locations', 'indents.location_id', '=', 'locations.id')
                        ->join('business_units', 'indents.business_unit_id', '=', 'business_units.id')
                        ->join('users', 'indents.created_by', '=', 'users.id');
            if (!auth()->user()->can('indent-view-all') && auth()->user()->can('indent-view-own')) {
                $indents = $indents->where('indents.created_by', auth()->user()->id);
            }

            if ($indentRequest['location_id'] != "") {
                $indents = $indents->where('indents.location_id', $indentRequest['location_id']);
            }

            if ($indentRequest['bill_mode'] != "") {
                $indents = $indents->where('bill_mode', $indentRequest['bill_mode']);
            }

            if ($indentRequest['business_unit_id'] != "") {
                $indents = $indents->where('business_unit_id', $indentRequest['business_unit_id']);
            }

            if ($indentRequest['status'] != "") {
                $indents = $indents->where('indents.status', $indentRequest['status']);
            }

            return DataTables::of($indents)
                ->addColumn('id', function ($row) {
                    return $row->indent_code;
                })
                ->addColumn('bill_mode', function ($row) {
                    return config('constants.BILL_MODES')[$row->bill_mode];
                })
                ->addColumn('status', function ($row) {
                    $statusClass = "bg-label-primary";
                    if ($row->status == "rejected") {
                        $statusClass = "bg-label-danger";
                    } else if ($row->status == "pending") {
                        $statusClass = "bg-label-warning";
                    } else if ($row->status == "closed") {
                        $statusClass = "bg-label-success";
                    } else if ($row->status == "approved") {
                        $statusClass = "bg-label-info";
                    }
                    return '<span class="badge '.$statusClass.' me-1">'.config('constants.INDENT_STATUS')[$row->status].'</span>';
                })
                ->addColumn('created_at', function ($row) {
                    return date('d-m-Y H:i:s', strtotime($row->created_at));
                })
                ->addColumn('action', function ($row) {
                    $actions = '<a class="dropdown-item" href="'.route('indent.show', $row->id).'"
                    ><i class="bx bx-show me-1"></i> View</a>';

                    if (auth()->user()->can('indent-edit') && !in_array($row->status, ['approved', 'rejected', 'closed'])) {
                        $actions .= '<a class="dropdown-item" href="'.route('indent.edit', $row->id).'"
                                        ><i class="bx bx-edit-alt me-1"></i> Edit</a>';
                    }

                    if (auth()->user()->can('indent-delete')) {
                        $actions .= '<button class="dropdown-item" onclick="deleteIndent('.$row->id.')"
                                        ><i class="bx bx-trash me-1"></i> Delete</button>
                                    <form id="'.$row->id.'" action="'.route('indent.destroy', $row->id).'" method="POST" class="d-none">
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
                ->rawColumns(['action', 'status'])
                ->make(true);

        }
        $locations = Location::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $businessUnits = BusinessUnit::select(['id', 'name'])->orderBy('name', 'asc')->get();
        return view('indent.index',compact('locations', 'businessUnits'));
    }

    public function create()
    {
        $locations = Location::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $businessUnits = BusinessUnit::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $expenses = Expense::join('indent_configurations', 'expenses.id', '=', 'indent_configurations.expense_id')->select(['expenses.id', 'expenses.name']);
        if (!auth()->user()->can('indent-view-all') && auth()->user()->can('indent-view-own')) {
            $expenses = $expenses->where('indent_configurations.user_id', auth()->user()->id);
        }
        $expenses = $expenses->orderBy('name', 'asc')->get();
        $paymentMethods = PaymentMethod::orderBy('name', 'asc')->get();
        return view('indent.create', compact('locations', 'businessUnits', 'expenses', 'paymentMethods'));
    }

    public function store(IndentRequest $request)
    {
        try {
            DB::beginTransaction();
            //Basic details
            $indentDetails = $request->only(['title', 'bill_mode', 'location_id', 'business_unit_id', 'softcopy_bill_submission_date', 'hardcopy_bill_submission_date', 'description']);
            $indent = $this->indentRepository->addIndent($indentDetails);

            //Item details
            $indentItemDetails = $request->only(['expense_id', 'vendor_id', 'quantity', 'unit_price', 'gst', 'tds']);
            $indent = $this->indentRepository->addIndentItems($indentItemDetails, $indent);


            //Payment details
            if ($request->has('payment_method_id')) {
                $indentPaymentDetails = $request->only(['payment_method_id', 'payment_description', 'amount']);
                $this->indentRepository->addIndentPayments($indentPaymentDetails, $indent);
            }

            //attachments
            if ($request->has('attachmentName') && $request->has('files')) {
                $attachmentNames = $request->input('attachmentName');
                $files = $request->file('files');
                $this->indentRepository->addIndentAttachments($files, $attachmentNames, $indent);
            }

            $this->indentRepository->updateIndentStatus($indent);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            \Log::emergency($e);
            return back()->withInput($request->input())->with('error', 'Something went wrong');
        }
        return redirect(route('indent.index'))->with('success', 'Indent Added Successfully');
    }

    public function edit($id)
    {
        $indent = Indent::where('id', $id)->with('indentItems', 'indentPayments', 'indentAttachments')->first();
        $locations = Location::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $businessUnits = BusinessUnit::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $expenses = Expense::join('indent_configurations', 'expenses.id', '=', 'indent_configurations.expense_id')->select(['expenses.id', 'expenses.name']);
        if (!auth()->user()->can('indent-view-all') && auth()->user()->can('indent-view-own')) {
            $expenses = $expenses->where('indent_configurations.user_id', auth()->user()->id);
        }
        $expenses = $expenses->orderBy('name', 'asc')->get();
        $paymentMethods = PaymentMethod::orderBy('name', 'asc')->get();
        return view('indent.edit', compact('indent', 'locations', 'businessUnits', 'expenses', 'paymentMethods'));
    }

    public function update(IndentRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $indent = Indent::where('id', $id)->with('indentItems', 'indentPayments', 'indentAttachments')->first();

            //Basic details
            $indentDetails = $request->only(['title', 'bill_mode', 'location_id', 'business_unit_id', 'softcopy_bill_submission_date', 'hardcopy_bill_submission_date', 'description']);
            $indent = $this->indentRepository->updateIndent($indentDetails, $indent);

            //Item details
            $indentItemDetails = $request->only(['expense_id', 'vendor_id', 'quantity', 'unit_price', 'indent_item_id', 'gst', 'tds']);
            $indent = $this->indentRepository->updateIndentItems($indentItemDetails, $indent);

            //Payment details
            if ($request->has('payment_method_id') || !empty($indent->indentPayments)) {
                $indentPaymentDetails = $request->only(['payment_method_id', 'payment_description', 'amount', 'indent_payment_id']);
                $this->indentRepository->updateIndentPayments($indentPaymentDetails, $indent);
            }

            //attachments
            if (($request->has('attachmentName') && $request->has('files')) || !empty($indent->indentAttachments)) {
                $attachmentNames = $request->input('attachmentName');
                $attachmentIds = $request->has('attachmentId') ? $request->input('attachmentId') : [];
                $files = $request->file('files');
                $this->indentRepository->updateIndentAttachments($files, $attachmentNames, $attachmentIds, $indent);
            }

            $this->indentRepository->updateIndentStatus($indent);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            \Log::emergency($e);
            return back()->withInput($request->input())->with('error', 'Something went wrong');
        }
        return redirect(route('indent.index'))->with('success', 'Indent Updated Successfully');
    }

    public function destroy($id)
    {
        Indent::where('id', $id)->delete();
        IndentItem::where('indent_id', $id)->delete();
        IndentPayment::where('indent_id', $id)->delete();
        // IndentAttachment::where('indent_id', $id)->delete();
        IndentComment::where('indent_id', $id)->delete();
        $indentAttachments = IndentAttachment::where('indent_id', $id)->get();
        if (!$indentAttachments->isEmpty()) {
            foreach ($indentAttachments as $attachment) {
                unlink(storage_path('app/'.$attachment->file_path));
                $attachment->delete();
            }
        }
        return redirect()->route('indent.index')->with('success', 'Indent Deleted Successfully');
    }

    public function indentApproval(Request $request)
    {
        if ($request->ajax()) {
            $indentRequest = $request->only(['location_id', 'bill_mode', 'business_unit_id', 'status']);

            $indents = $this->indentRepository->getIndentApproval("", $indentRequest);
            return DataTables::of($indents)
                ->addColumn("approval", function ($row) {
                    return '<div class="form-check mt-3">
                                <input class="form-check-input indent_approval" name="indent_approval[]" type="checkbox" value="'.$row->indent_item_id.'">
                            </div>';
                })
                ->addColumn('id', function ($row) {
                    return $row->indent_code;
                })
                ->addColumn('bill_mode', function ($row) {
                    return config('constants.BILL_MODES')[$row->bill_mode];
                })
                ->addColumn('status', function ($row) {
                    return config('constants.INDENT_ITEM_STATUS')[$row->status];
                })
                ->addColumn('created_at', function ($row) {
                    return date('d-m-Y H:i:s', strtotime($row->created_at));
                })
                ->addColumn('action', function ($row) {
                    return '<a class="btn rounded-pill btn-outline-primary" href="'.route('indent.show', $row->id).'"
                    ><i class="bx bx-show me-1"></i> View</a>';
                })
                ->rawColumns(['action', 'approval'])
                ->make(true);

        }
        $locations = Location::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $businessUnits = BusinessUnit::select(['id', 'name'])->orderBy('name', 'asc')->get();
        return view('indent.indent_approvals', compact('locations', 'businessUnits'));
    }

    public function show($id)
    {
        $paymentMethods = PaymentMethod::orderBy('name', 'asc')->get();
        $indent = Indent::with('indentItems', 'indentPayments', 'indentAttachments', 'location', 'businessUnit', 'indentComments', 'indentApproveLogs')->where('id', $id)->first();
        return view('indent.show', compact('indent', 'paymentMethods'));
    }

    public function indentComment(Request $request)
    {
        $comment = new IndentComment();
        $comment->comment = $request->comment;
        $comment->indent_id = $request->indent_id;
        $comment->save();
    }

    public function UpdateIndentItemStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $indentItemId = $request->input('indent_item_id');
            $status = $request->input('status');
            $indentItem = IndentItem::with('expense')->find($indentItemId);
            if ($status == "rejected") {
                $comment = new IndentComment();
                $comment->comment = $request->input('comment');
                $comment->indent_id = $indentItem->indent_id;
                $comment->save();
                $this->indentRepository->updateIndentItemStatus($status, $indentItem);
                $indentItem->next_approver_id = 0;
                $indentItem->save();
            } else {
                $this->indentRepository->updateIndentItemToNextApproval($status, $indentItem);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            \Log::emergency($e);
        }
    }

    public function indentClosure(Request $request)
    {
        if ($request->ajax()) {
            $indentRequest = $request->only(['location_id', 'bill_mode', 'business_unit_id', 'status']);
            $indents = Indent::select(['indents.id', 'indents.title', 'locations.name as location', 'business_units.name as business_unit', 'bill_mode', 'indents.total', 'indents.status', 'indents.created_at', 'users.name as raised_by'])
                        ->join('locations', 'indents.location_id', '=', 'locations.id')
                        ->join('business_units', 'indents.business_unit_id', '=', 'business_units.id')
                        ->join('users', 'indents.created_by', '=', 'users.id')
                        ->whereIn('indents.status', ['approved', 'half-approved']);
            if ($indentRequest['location_id'] != "") {
                $indents = $indents->where('indents.location_id', $indentRequest['location_id']);
            }

            if ($indentRequest['bill_mode'] != "") {
                $indents = $indents->where('bill_mode', $indentRequest['bill_mode']);
            }

            if ($indentRequest['business_unit_id'] != "") {
                $indents = $indents->where('business_unit_id', $indentRequest['business_unit_id']);
            }


            return DataTables::of($indents)
                ->addColumn('id', function ($row) {
                    return $row->indent_code;
                })
                ->addColumn('bill_mode', function ($row) {
                    return config('constants.BILL_MODES')[$row->bill_mode];
                })
                ->addColumn('status', function ($row) {
                    return config('constants.INDENT_STATUS')[$row->status];
                })
                ->addColumn('created_at', function ($row) {
                    return date('d-m-Y H:i:s', strtotime($row->created_at));
                })
                ->addColumn('close', function ($row) {
                    return '<div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" value="" id="defaultCheck1" onclick="closeIndent('.$row->id.')">
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    return '<a class="btn rounded-pill btn-outline-primary" href="'.route('indent.show', $row->id).'"
                    ><i class="bx bx-show me-1"></i> View</a>';
                })
                ->rawColumns(['action', 'close'])
                ->make(true);

        }
        $locations = Location::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $businessUnits = BusinessUnit::select(['id', 'name'])->orderBy('name', 'asc')->get();
        return view('indent.indent_closure', compact('locations', 'businessUnits'));
    }

    public function closeIndent($id)
    {
        Indent::where('id', $id)->update(['status' => 'closed']);
    }

    public function updatePayment(Request $request, $id)
    {
        $indent = Indent::with('indentPayments')->find($id);
        if ($request->has('payment_method_id')) {
            $indentPaymentDetails = $request->only(['payment_method_id', 'payment_description', 'amount', 'indent_payment_id']);
            $this->indentRepository->updateIndentPayments($indentPaymentDetails, $indent, true);
            return back()->withInput($request->input())->with('success', 'Indent payment updated successfully.');
        }
        return back()->withInput($request->input())->with('error', 'No indent payment to add.');
    }

    public function bulkIndentItemApprove(Request $request)
    {
        $indentApprovals = $request->input('indent_approval');
        if (!empty($indentApprovals)) {
            foreach ($indentApprovals as $indentItemId) {
                $indentItem = IndentItem::with('expense')->find($indentItemId);
                $this->indentRepository->updateIndentItemToNextApproval("approved", $indentItem);
            }
        }
    }

}

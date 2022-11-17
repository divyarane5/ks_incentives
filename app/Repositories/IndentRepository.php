<?php
namespace App\Repositories;

use App\Interfaces\IndentRepositoryInterface;
use App\Mail\IndentApprovalEmail;
use App\Models\Indent;
use App\Models\IndentApproveLog;
use App\Models\IndentAttachment;
use App\Models\IndentConfiguration;
use App\Models\IndentItem;
use App\Models\IndentPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Exception;

class IndentRepository implements IndentRepositoryInterface
{
    public function addIndent($indentDetails)
    {
        $indent = new Indent();
        $indent->title = $indentDetails['title'];
        $indent->description = $indentDetails['description'];
        $indent->location_id = $indentDetails['location_id'];
        $indent->business_unit_id = $indentDetails['business_unit_id'];
        $indent->bill_mode = $indentDetails['bill_mode'];
        $indent->softcopy_bill_submission_date = $indentDetails['softcopy_bill_submission_date'];
        $indent->hardcopy_bill_submission_date = $indentDetails['hardcopy_bill_submission_date'];
        $indent->save();
        return $indent;
    }

    public function addIndentItems($indentDetails, $indent)
    {
        $itemsCount = sizeof($indentDetails['expense_id']);
        $total = 0;
        $approvalPending = 0;
        for ($i = 0; $i < $itemsCount; $i++) {
            $indentArr = [$indentDetails['expense_id'][$i], $indentDetails['vendor_id'][$i], $indentDetails['quantity'][$i], $indentDetails['unit_price'][$i]];
            if (!in_array("", $indentArr) && !in_array(null, $indentArr)) {
                $gst = ($indentDetails['gst'][$i] != "") ? $indentDetails['gst'][$i] : 0;
                $tds = ($indentDetails['tds'][$i] != "") ? $indentDetails['tds'][$i] : 0;
                $indentItem = new IndentItem();
                $indentItem->indent_id = $indent->id;
                $indentItem->expense_id = $indentDetails['expense_id'][$i];
                $indentItem->vendor_id = $indentDetails['vendor_id'][$i];
                $indentItem->quantity = $indentDetails['quantity'][$i];
                $indentItem->unit_price = $indentDetails['unit_price'][$i];
                $indentItem->gst = $gst;
                $indentItem->tds = $tds;
                $subTotal = ($indentItem->quantity*$indentItem->unit_price);
                $total = (($subTotal + $gst) - $tds);
                $indentItem->total = $total;
                $indentItem->save();
                $total += $total;
                $this->updateIndentItemApproval($indentItem);

            }
        }
        $indent->total = $total;
        $indent->save();
        return $indent;
    }

    public function addIndentPayments($indentPaymentDetails, $indent)
    {
        $itemsCount = sizeof($indentPaymentDetails['payment_method_id']);
        for ($i = 0; $i < $itemsCount; $i++) {
            $paymentArr = [$indentPaymentDetails['payment_method_id'][$i], $indentPaymentDetails['amount'][$i]];
            if (!in_array("", $paymentArr) && !in_array(null, $paymentArr)) {
                $indentPayment = new IndentPayment();
                $indentPayment->indent_id = $indent->id;
                $indentPayment->payment_method_id = $indentPaymentDetails['payment_method_id'][$i];
                $indentPayment->description = $indentPaymentDetails['payment_description'][$i];
                $indentPayment->amount = $indentPaymentDetails['amount'][$i];
                $indentPayment->save();
            }
        }
    }

    public function addIndentAttachments($files, $attachmentNames, $indent)
    {
        if (!empty($files)) {
            foreach ($files as $file) {
                $fileName = $file->getClientOriginalName();
                if (in_array($fileName, $attachmentNames)) {
                    $path = uploadFile($file, config('uploadfilepath.INDENT_FILES'));
                    $indentAttachment = new IndentAttachment();
                    $indentAttachment->indent_id = $indent->id;
                    $indentAttachment->file_path = $path;
                    $indentAttachment->file_name = $fileName;
                    $indentAttachment->save();
                }
            }
        }
    }

    public function checkForAutoApprove($indent)
    {
        $total = $indent->total;
    }

    public function updateIndent($indentDetails, $indent)
    {
        $indent->title = $indentDetails['title'];
        $indent->description = $indentDetails['description'];
        $indent->location_id = $indentDetails['location_id'];
        $indent->business_unit_id = $indentDetails['business_unit_id'];
        $indent->bill_mode = $indentDetails['bill_mode'];
        $indent->softcopy_bill_submission_date = $indentDetails['softcopy_bill_submission_date'];
        $indent->hardcopy_bill_submission_date = $indentDetails['hardcopy_bill_submission_date'];
        $indent->save();
        return $indent;
    }

    public function updateIndentItems($indentItemDetails, $indent)
    {
        $oldIndentItemsIds = $indent->indentItems->pluck('id')->toArray();
        $newIndentItemsIds = array_filter($indentItemDetails['indent_item_id'], 'strlen');
        $deletedItemsIds = array_diff($oldIndentItemsIds, $newIndentItemsIds);

        //delete removed items
        IndentItem::whereIn('id', $deletedItemsIds)->forceDelete();

        //update items
        $total = 0;
        for ($i = 0; $i < sizeof($indentItemDetails['indent_item_id']); $i++) {
            $gst = ($indentItemDetails['gst'][$i] != "") ? $indentItemDetails['gst'][$i] : 0;
            $tds = ($indentItemDetails['tds'][$i] != "") ? $indentItemDetails['tds'][$i] : 0;
            $indentItem = new IndentItem();
            if (!empty($indentItemDetails['indent_item_id'][$i])) {
                $indentItem = IndentItem::find($indentItemDetails['indent_item_id'][$i]);
            }
            $indentItem->indent_id = $indent->id;
            $indentItem->expense_id = $indentItemDetails['expense_id'][$i];
            $indentItem->vendor_id = $indentItemDetails['vendor_id'][$i];
            $indentItem->quantity = $indentItemDetails['quantity'][$i];
            $indentItem->unit_price = $indentItemDetails['unit_price'][$i];
            $indentItem->gst = $gst;
            $indentItem->tds = $tds;
            $subTotal = ($indentItem->quantity*$indentItem->unit_price);
            $total = (($subTotal + $gst) - $tds);
            $indentItem->total = $total;
            $indentItem->save();
            $total += $indentItem->total;

            $this->updateIndentItemApproval($indentItem);
        }
        $indent->total = $total;
        $indent->save();
        return $indent;
    }

    public function updateIndentPayments($indentPaymentDetails, $indent, $addingFlag = false)
    {
        $oldIndentPaymentIds = $indent->indentPayments->pluck('id')->toArray();
        $newIndentPaymentIds = isset($indentPaymentDetails['indent_payment_id']) ? array_filter($indentPaymentDetails['indent_payment_id'], 'strlen') : [];
        $deletedPaymentIds = array_diff($oldIndentPaymentIds, $newIndentPaymentIds);

        if (!empty($deletedPaymentIds) && !$addingFlag) {
            IndentPayment::whereIn('id', $deletedPaymentIds)->forceDelete();
        }

        if (!empty($indentPaymentDetails['indent_payment_id'])) {
            for ($i = 0; $i < sizeof($indentPaymentDetails['indent_payment_id']); $i++) {
                $indentPayment = new IndentPayment();
                if (!empty($indentPaymentDetails['indent_payment_id'][$i])) {
                    $indentPayment = IndentPayment::find($indentPaymentDetails['indent_payment_id'][$i]);
                }
                $indentPayment->indent_id = $indent->id;
                $indentPayment->payment_method_id = $indentPaymentDetails['payment_method_id'][$i];
                $indentPayment->description = $indentPaymentDetails['payment_description'][$i];
                $indentPayment->amount = $indentPaymentDetails['amount'][$i];
                $indentPayment->save();
            }
        }
    }

    public function updateIndentAttachments($files, $attachmentNames, $attachmentIds, $indent)
    {
        $oldIndentAttachmentIds = $indent->indentAttachments->pluck('id')->toArray();
        $newIndentAttachmentIds = $attachmentIds;
        $deletedAttachmentIds = array_diff($oldIndentAttachmentIds, $newIndentAttachmentIds);

        if (!empty($deletedAttachmentIds)) {
            $deletedIndentAttachments = IndentAttachment::whereIn('id', $deletedAttachmentIds)->get()->pluck('file_path');
            foreach ($deletedIndentAttachments as $attachments) {
                unlink(storage_path('app/'.$attachments));
            }
            IndentAttachment::whereIn('id', $deletedAttachmentIds)->forceDelete();
        }

        if (!empty($files)) {
            foreach ($files as $file) {
                $fileName = $file->getClientOriginalName();
                if (in_array($fileName, $attachmentNames)) {
                    $path = uploadFile($file, config('uploadfilepath.INDENT_FILES'));
                    $indentAttachment = new IndentAttachment();
                    $indentAttachment->indent_id = $indent->id;
                    $indentAttachment->file_path = $path;
                    $indentAttachment->file_name = $fileName;
                    $indentAttachment->save();
                }
            }
        }
    }

    public function updateIndentItemApproval($indentItem)
    {
        $configuration = IndentConfiguration::where('user_id', $indentItem->created_by)->where('expense_id', $indentItem->expense_id)->first();
        if (!empty($configuration)) {
            if ($indentItem->status == 'pending' || $indentItem->status == '') {
                $approvalRequired = 0;
                $firstApprover = $configuration->approver1;
                $indentMonthlyTotal = IndentItem::where('created_by', $indentItem->created_by)
                                        ->where('expense_id', $indentItem->expense_id)
                                        ->where('status', '!=', 'rejected')
                                        ->whereMonth('created_at', Carbon::now()->month)
                                        ->sum('total');

                if ($indentMonthlyTotal > $configuration->monthly_limit) {
                    $approvalRequired = 1;
                }

                if ($indentItem->unit_price > $configuration->indent_limit) {
                    $approvalRequired = 1;
                }

                if ($approvalRequired == 0 && !empty($firstApprover)) {
                    $desc = $indentItem->expense->name." is marked as approved.";
                    return $this->updateIndentItemStatus('approved', $indentItem, $desc);
                }

                $indentItem->next_approver_id = $firstApprover;
                $indentItem->save();
                $this->indentApprovalEmail($indentItem);
                return $indentItem;
            }
        }
        return $indentItem;
    }

    public function updateIndentStatus($indent)
    {
        $indentItemsStatus = IndentItem::select('status', DB::raw('count(status) as total'))->where('indent_id', $indent->id)->groupBy('status')->get()->pluck('total', 'status')->toArray();

        $indent->status = 'pending';

        if (sizeof($indentItemsStatus) == 1 && isset($indentItemsStatus['approved'])) {
            $indent->status = 'approved';
        }

        if (sizeof($indentItemsStatus) == 1 && isset($indentItemsStatus['rejected'])) {
            $indent->status = 'rejected';
        }

        if (sizeof($indentItemsStatus) == 2 && isset($indentItemsStatus['approved']) && isset($indentItemsStatus['rejected'])) {
            $indent->status = 'half-approved';
        }

        $indent->save();
        return $indent;
    }

    public function updateIndentItemStatus($status, $indentItem, $desc = "")
    {
        $indentItem->status = $status;
        $indentItem->save();
        $indentApproveLog = new IndentApproveLog();
        $indentApproveLog->indent_item_id = $indentItem->id;
        $indentApproveLog->user_id = auth()->user()->id;
        $indentApproveLog->status = $status;
        $indentApproveLog->description = ($desc != "") ? $desc : $indentItem->expense->name.' is '.$status.' by '.auth()->user()->name;
        $indentApproveLog->save();
        $indent = Indent::find($indentItem->indent_id);
        $this->updateIndentStatus($indent);
        return $indentItem;
    }

    public function updateIndentItemToNextApproval($status, $indentItem)
    {
        $configuration = IndentConfiguration::where('user_id', $indentItem->created_by)->where('expense_id', $indentItem->expense_id)->first();
        if (!empty($configuration)) {
            $configuration = $configuration->toArray();
            if ($status == 'approved') {
                //next status
                if ($indentItem->status == "pending") {
                    $nextStatus = "approve1";
                } else if (str_contains($indentItem->status, 'approve')) {
                    $level = substr($indentItem->status, -1);
                    $level = is_numeric($level) ? $level : '';
                    $nextStatus = ($level != "") ? "approve".($level+1) : '';
                }

                if ($nextStatus != "") {
                    //next approver
                    $nextApproverId = "";
                    $nextApproverColumn = config('constants.INDENT_ITEM_COLUMN_MAPPING')[$nextStatus];
                    if ($nextApproverColumn != "") {
                        $nextApproverId = $configuration[$nextApproverColumn];
                    }
                    if ($nextApproverId == "" || $nextApproverId == null) {
                        $this->updateIndentItemStatus('approved', $indentItem);
                        $indentItem->next_approver_id = 0;
                        $indentItem->save();
                        return $indentItem;
                    }
                    $desc = $indentItem->expense->name.' is approved by '.auth()->user()->name;
                    $this->updateIndentItemStatus($nextStatus, $indentItem, $desc);
                    $indentItem->next_approver_id = $nextApproverId;
                    $indentItem->save();
                    $this->indentApprovalEmail($indentItem);
                }
            }
        } else {
            if ($status == 'approved') {
                $this->updateIndentItemStatus('approved', $indentItem);
                $indentItem->next_approver_id = 0;
                $indentItem->save();
                return $indentItem;
            }
        }
    }

    public function indentApprovalEmail($indentItem)
    {
        try {
            $user = User::find($indentItem->created_by);
            $arr = [
                'user' => $user->name,
                'indent_id' => $indentItem->indent_id,
                'indent_code' => $indentItem->indent->indent_code
            ];
            $approvalToEmails = User::whereIn('id', explode(",", $indentItem->next_approver_id))->get()->pluck('email')->toArray();
            Mail::to($approvalToEmails)->send(new IndentApprovalEmail($arr)); //$approvalToEmails
        } catch (Exception $e) {
            \Log::emergency($e);
        }
    }

    public function IndentCountByStatus()
    {
        $indents = Indent::select(['status', DB::raw('count(*) as total')]);
        if (!auth()->user()->can('indent-view-all') && auth()->user()->can('indent-view-own')) {
            $indents = $indents->where('indents.created_by', auth()->user()->id);
        }
        return $indents->groupBy('status')->get()->pluck('total', 'status')->toArray();
    }

    public function getWeeklyIndentExpense()
    {
        $indentExpense =IndentPayment::select([DB::raw('SUM(amount) as expense'), DB::raw('DATE_FORMAT(indent_payments.created_at, "%Y-%m-%d") as created_date')])
                            ->join('indents', 'indent_payments.indent_id', '=', 'indents.id');
        if (!auth()->user()->can('indent-view-all') && auth()->user()->can('indent-view-own') && !auth()->user()->can('indent-payment-conclude')) {
            $indentExpense = $indentExpense->where('indents.created_by', auth()->user()->id);
        }
        return $indentExpense->whereBetween(DB::raw('DATE_FORMAT(indent_payments.created_at, "%Y-%m-%d")'), [date('Y-m-d', strtotime('-6 days')), date('Y-m-d')])
                            ->groupBy('created_date')
                            ->get()
                            ->pluck('expense', 'created_date')
                            ->toArray();
    }

    public function getTotalIndentExpense()
    {
        $indentExpense =IndentPayment::select([DB::raw('SUM(amount) as total')])
                            ->join('indents', 'indent_payments.indent_id', '=', 'indents.id');
        if (!auth()->user()->can('indent-view-all') && auth()->user()->can('indent-view-own') && !auth()->user()->can('indent-payment-conclude')) {
            $indentExpense = $indentExpense->where('indents.created_by', auth()->user()->id);
        }
        return $indentExpense->first()->total;
    }

    public function getIndentApproval($limit = "", $indentRequest = [])
    {
        $indents = Indent::select(['indents.id', 'indent_items.id as indent_item_id', 'expenses.name as expense', 'vendors.name as vendor', 'indents.title', 'locations.name as location', 'business_units.name as business_unit', 'bill_mode', 'indent_items.total', 'indent_items.status', 'indent_items.created_at', 'users.name as raised_by'])
                        ->join('locations', 'indents.location_id', '=', 'locations.id')
                        ->join('business_units', 'indents.business_unit_id', '=', 'business_units.id')
                        ->join('indent_items', 'indents.id', '=', 'indent_items.indent_id')
                        ->join('expenses', 'indent_items.expense_id', '=', 'expenses.id')
                        ->join('vendors', 'indent_items.vendor_id', '=', 'vendors.id')
                        ->join('users', 'indents.created_by', '=', 'users.id')
                        ->whereNotIn('indent_items.status', ['rejected', 'approved', 'closed']);
        if (!auth()->user()->hasRole('Superadmin')) {
            $indents = $indents->whereRaw('FIND_IN_SET("'.auth()->user()->id.'", indent_items.next_approver_id)');
        }

        if (isset($indentRequest['location_id']) && $indentRequest['location_id'] != "") {
            $indents = $indents->where('indents.location_id', $indentRequest['location_id']);
        }

        if (isset($indentRequest['bill_mode']) && $indentRequest['bill_mode'] != "") {
            $indents = $indents->where('bill_mode', $indentRequest['bill_mode']);
        }

        if (isset($indentRequest['business_unit_id']) && $indentRequest['business_unit_id'] != "") {
            $indents = $indents->where('business_unit_id', $indentRequest['business_unit_id']);
        }

        if (isset($indentRequest['status']) && $indentRequest['status'] != "") {
            $indents = $indents->where('indents.status', $indentRequest['status']);
        }

        if ($limit != "") {
            $indents = $indents->limit($limit);
        }
        return $indents;
    }
}

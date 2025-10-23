<?php

namespace App\Http\Controllers;

use App\Models\BusinessUnit;
use App\Models\Indent;
use App\Models\Location;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use DataTables;

class ReportController extends Controller
{
    public function indentPayments(Request $request)
    {
        if ($request->ajax()) {
            $indentRequest = $request->only(['location_id', 'bill_mode', 'business_unit_id', 'status']);
            $indents = Indent::select(["indents.id", "indents.title", "users.name as payment_by", "payment_methods.name as payment_method", "indent_payments.amount", "indent_payments.created_at"])
                    ->join('indent_payments', 'indent_payments.indent_id', '=', 'indents.id')
                    ->join('users', 'indent_payments.created_by', '=', 'users.id')
                    ->join('payment_methods', 'indent_payments.payment_method_id', '=', 'payment_methods.id');
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
                    ->addColumn('code', function ($row) {
                        return '<a target="_blank" href="'.route('indent.show', $row->id).'" >'.$row->indent_code.'</a>';
                    })
                    ->addColumn('created_at', function ($row) {
                        return date("Y-m-d H:i:s", strtotime($row->created_at));
                    })
                    ->rawColumns(['code'])
                    ->make();
        }

        $locations = Location::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $businessUnits = BusinessUnit::select(['id', 'name'])->orderBy('name', 'asc')->get();
        return view('reports.indent_payments', compact('locations', 'businessUnits'));
    }

    public function reimbursementPayments(Request $request)
    {
        if ($request->ajax()) {
            $reimbursements = Reimbursement::select(['reimbursements.id', 'client_name', 'project_name', 'attended_of.name as visit_attended_of_user', 'source', 'destination', 'settlement_amount', 'settled_by_user.name as settled_by', 'reimbursement_logs.created_at as settled_at', 'created_by_user.name as visit_attended_by'])
                                ->join('users as attended_of', 'reimbursements.visit_attended_of_id', '=', 'attended_of.id')
                                ->join('users as created_by_user', 'reimbursements.created_by', '=', 'created_by_user.id')
                                ->join('reimbursement_logs', 'reimbursements.id', '=', 'reimbursement_logs.reimbursement_id')
                                ->join('users as settled_by_user', 'reimbursement_logs.created_by', '=', 'settled_by_user.id');
            if (!auth()->user()->can('reimbursement-view-all') && !auth()->user()->can('reimbursement-settlement') && auth()->user()->can('reimbursement-view-own')) {
                $reimbursements = $reimbursements->where(function($query) {
                                    $query = $query->where('reimbursements.created_by', auth()->user()->id)
                                        ->orWhere('created_by_user.reporting_manager_id ', auth()->user()->id);
                                });
            }
            $reimbursements = $reimbursements->where('reimbursements.status', 'settled')->where('reimbursement_logs.status', 'settled')->get();
            return DataTables::of($reimbursements)
                                ->addColumn('code', function ($row) {
                                    return '<a target="_blank" href="'.route('reimbursement.show', $row->id).'" >'.$row->reimbursement_code.'</a>';
                                })
                                ->addColumn('settled_at', function ($row) {
                                    return date("Y-m-d H:i:s", strtotime($row->settled_at));
                                })
                                ->rawColumns(['code'])
                                ->make();
        }
        return view('reports.reimbursement_payments');
    }
}

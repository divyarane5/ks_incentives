<?php
namespace App\Repositories;

use App\Interfaces\ReimbursementRepositoryInterface;
use App\Models\Reimbursement;
use App\Models\ReimbursementLog;
use DB;

class ReimbursementRepository implements ReimbursementRepositoryInterface
{
    public function updateReimbursementStatus($reimbursement, $status, $request)
    {
        $reimbursement->status = $status;
        if ($status == "settled") {
            $reimbursement->settlement_amount = $request->input('settlement_amount');
            $reimbursement->settlement_comment = $request->input('settlement_comment');
        }
        $reimbursement->save();
        $reimbursementLog = new ReimbursementLog();
        $reimbursementLog->reimbursement_id = $reimbursement->id;
        $reimbursementLog->status = $status;
        $reimbursementLog->description = "Reimbursement ".$status." by ".auth()->user()->name;
        $reimbursementLog->save();
    }

    public function getWeeklyReimbursementExpense()
    {
        $reimbursementExpense = Reimbursement::select(DB::raw('SUM(settlement_amount) as expense'), DB::raw('DATE_FORMAT(reimbursement_logs.created_at, "%Y-%m-%d") as created_date'))
                                    ->join('reimbursement_logs', 'reimbursements.id', '=', 'reimbursement_logs.reimbursement_id');
        if (!auth()->user()->can('reimbursement-view-all') && auth()->user()->can('reimbursement-view-own') && !auth()->user()->can('reimbursement-settlement')) {
            $reimbursementExpense = $reimbursementExpense->where('reimbursements.created_by', auth()->user()->id);
        }
        return $reimbursementExpense->whereBetween(DB::raw('DATE_FORMAT(reimbursement_logs.created_at, "%Y-%m-%d")'), [date('Y-m-d', strtotime('-6 days')), date('Y-m-d')])
                                    ->groupBy('created_date')
                                    ->get()
                                    ->pluck('expense', 'created_date')
                                    ->toArray();
    }

    public function getTotalReimbursementExpense()
    {
        $reimbursementExpense = Reimbursement::select(DB::raw('SUM(settlement_amount) as total'))
                                    ->join('reimbursement_logs', 'reimbursements.id', '=', 'reimbursement_logs.reimbursement_id');
        if (!auth()->user()->can('reimbursement-view-all') && auth()->user()->can('reimbursement-view-own') && !auth()->user()->can('reimbursement-settlement')) {
            $reimbursementExpense = $reimbursementExpense->where('reimbursements.created_by', auth()->user()->id);
        }
        return $reimbursementExpense->first()->total;
    }

    public function getReimbursementApproval()
    {
        $reimbursements = Reimbursement::select(['reimbursements.*', 'attended_of.name as visit_attended_of_user', 'created_by_user.name as visit_created_by', 'created_by_user.reporting_manager_id'])
                                ->join('users as attended_of', 'reimbursements.visit_attended_of_id', '=', 'attended_of.id')
                                ->join('users as created_by_user', 'reimbursements.created_by', '=', 'created_by_user.id')
                                ->where('reimbursements.status', 'pending');

        if (!auth()->user()->hasRole('Superadmin')) {
            $reimbursements = $reimbursements->where('created_by_user.reporting_manager_id', auth()->user()->id);
        }
        return $reimbursements;
    }
} 

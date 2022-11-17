<?php
namespace App\Interfaces;

interface ReimbursementRepositoryInterface
{
    public function updateReimbursementStatus($reimbursement, $status, $request);

    //dashboard
    public function getWeeklyReimbursementExpense();
    public function getTotalReimbursementExpense();
    public function getReimbursementApproval();
}

<?php

namespace App\Http\Controllers;

use App\Interfaces\IndentRepositoryInterface;
use App\Interfaces\ReimbursementRepositoryInterface;
use App\Models\Indent;
use App\Models\IndentPayment;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{
    private $indentRepository;
    private $reimbursementRepository;
    function __construct(IndentRepositoryInterface $indentRepository, ReimbursementRepositoryInterface $reimbursementRepository)
    {
        $this->indentRepository = $indentRepository;
        $this->reimbursementRepository = $reimbursementRepository;
    }

    public function index()
    {
        //Indent count by status
        $indents = $this->indentRepository->IndentCountByStatus();

        //last 7 days indent expense
        $indentExpense = $this->indentRepository->getWeeklyIndentExpense();

        //last 7 days reimbursement expense
        $reimbursementExpense = $this->reimbursementRepository->getWeeklyReimbursementExpense();

        //Array of indent and reimbursement
        $indentExpenseArray = [];
        $reimbursementExpenseArray = [];
        $totalWeeklyExpenseArray = [];
        for ($i = 6; $i>=0; $i--) {
            $date = date('Y-m-d', strtotime('-'.$i.' days'));
            $indentExp = floatval((isset($indentExpense[$date])) ? $indentExpense[$date] : 0);
            $reimbursementExp = floatval((isset($reimbursementExpense[$date])) ? $reimbursementExpense[$date] : 0);
            $indentExpenseArray[] = $indentExp;
            $reimbursementExpenseArray[] = $reimbursementExp;
            $totalWeeklyExpenseArray[] = [
                                "x" => date('d-m-Y', strtotime('-'.$i.' days')),
                                "y" => $indentExp+$reimbursementExp
                            ];
        }
        $indentExpenseArray = $indentExpenseArray;
        $reimbursementExpenseArray = $reimbursementExpenseArray;

        //Indent expense
        $totalIndentExpense = $this->indentRepository->getTotalIndentExpense();
        $totalReimbursementExpense = $this->reimbursementRepository->getTotalReimbursementExpense();

        //Indent Approval
        $indentApproval = [];
        if (auth()->user()->can('indent-approval')) {
            $indentApproval = $this->indentRepository->getIndentApproval(10)->orderBy('indents.id', 'desc')->get();
        }

        $reimbursementApproval = [];
        if (auth()->user()->can('reimbursement-approval')) {
            $reimbursementApproval = $this->reimbursementRepository->getReimbursementApproval()->orderBy('reimbursements.id', 'desc')->get();
        }

        return view('dashboard', compact(
            'indents',
            'indentExpenseArray',
            'reimbursementExpenseArray',
            'totalIndentExpense',
            'totalReimbursementExpense',
            'totalWeeklyExpenseArray',
            'indentApproval',
            'reimbursementApproval'
        ));
    }




}

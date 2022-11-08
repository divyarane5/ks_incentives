<?php
namespace App\Repositories;

use App\Interfaces\ExpenseRepositoryInterface;
use App\Models\Expense;
use App\Models\ExpenseVendorMapping;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function addExpense($request)
    {
        //create expense
        $expense = new Expense();
        $expense->name = $request['name'];
        $expense->save();

        //mapping
        $expenseVendorMapping = [];
        $vendors = $request['vendors'];
        if (!empty($vendors)) {
            foreach ($vendors as $vendor) {
                $expenseVendorMapping[] = [
                    'expense_id' => $expense->id,
                    'vendor_id' => $vendor
                ];
            }
            ExpenseVendorMapping::insert($expenseVendorMapping);
        }
        return $expense;
    }
}

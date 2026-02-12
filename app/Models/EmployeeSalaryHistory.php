<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'annual_ctc', 'monthly_basic', 'monthly_hra', 
        'special_allowance', 'conveyance_allowance', 'medical_reimbursement',
        'professional_tax', 'pf_employer', 'pf_employee', 'net_deductions',
        'net_salary', 'effective_from', 'changed_by'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeReportingManagerHistory extends Model
{
    use HasFactory;

    protected $table = 'employee_reporting_manager_histories';

    protected $fillable = [
        'user_id',
        'reporting_manager_id',
        'effective_from',
        'effective_to',
        'changed_by',
    ];

    // EmployeeReportingManagerHistory.php
    public function manager()
    {
        return $this->belongsTo(User::class, 'reporting_manager_id');
    }
}

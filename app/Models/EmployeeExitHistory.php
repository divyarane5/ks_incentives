<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeExitHistory extends Model
{
    use HasFactory;

    protected $table = 'employee_exit_histories'; // make sure your table name matches

    protected $fillable = [
         'user_id',
        'exit_date',
        'exit_type',
        'exit_reason',
        'remarks',
        'approved_by',
        'is_rehirable',
    ];
}

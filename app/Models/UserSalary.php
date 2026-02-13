<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSalary extends Model
{
    use HasFactory;

    protected $fillable = [
         'user_id',
        'financial_year',
        'month',
        'gross_salary',
        'professional_tax',
        'pf_amount',
        'extra_deduction',
        'tds', 
        'system_net_salary',
        'salary_credited',
        'total_employee_cost',
        'status',
        'remarks',
    ];

    protected $casts = [
        'credited_on' => 'date',
    ];

    /* =========================
     | Relationships
     ========================= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

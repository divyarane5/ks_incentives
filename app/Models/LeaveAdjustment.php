<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveAdjustment extends Model
{
    protected $fillable = ['user_id', 'leave_type_id', 'financial_year', 'days', 'note', 'created_by'];

    protected $casts = ['days' => 'float'];

    public function type()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

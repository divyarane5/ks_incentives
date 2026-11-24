<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = ['name', 'code', 'yearly_quota', 'is_paid', 'carry_forward_max', 'color', 'active', 'sort'];

    protected $casts = ['is_paid' => 'boolean', 'active' => 'boolean', 'yearly_quota' => 'float', 'carry_forward_max' => 'float'];

    public function scopeActive($q)
    {
        return $q->where('active', true)->orderBy('sort')->orderBy('name');
    }
}

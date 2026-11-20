<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncentivePayout extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'details' => 'array',
        'frozen_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function frozenBy()
    {
        return $this->belongsTo(User::class, 'frozen_by');
    }
}

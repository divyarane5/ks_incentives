<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncentiveSlab extends Model
{
    use HasFactory;

    protected $fillable = [
        'financial_year',
        'role',
        'from_times',
        'to_times',
        'incentive_percent',
        'justification_multiplier',
    ];
}
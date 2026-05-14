<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncentiveCalculation extends Model
{
    protected $fillable = [

        'user_id',

        'financial_year',

        'role',

        'annual_salary',

        'booking_collection',

        'performance_times',

        'justification_amount',

        'eligible_collection',

        'incentive_percent',

        'final_incentive',
    ];
}
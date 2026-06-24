<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyBankAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_name',
        'bank_name',
        'account_number',
        'ifsc',
        'gstin',
        'status'
    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class ExpenseVendorMapping extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'expense_vendor_mapping';

    protected $fillable = [
        'expense_id',
        'vendor_id'
    ];


}

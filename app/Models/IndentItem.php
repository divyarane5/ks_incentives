<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class IndentItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'indent_id',
        'expense_id',
        'vendor_id',
        'quantity',
        'unit_price',
        'total'
    ];

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = is_object(Auth::user()) ? Auth::user()->id : 1;
        });
    }
}

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
        'total',
        'next_approver_id',
        'gst',
        'tds'
    ];

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = is_object(Auth::user()) ? Auth::user()->id : 1;
        });
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function indent()
    {
        return $this->belongsTo(Indent::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}

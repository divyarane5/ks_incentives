<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Indent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'location_id',
        'business_unit_id',
        'bill_mode',
        'softcopy_bill_submission_date',
        'hardcopy_bill_submission_date',
        'total',
        'status'
    ];

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = is_object(Auth::user()) ? Auth::user()->id : 1;
        });
    }

    public function indentItems()
    {
        return $this->hasMany(IndentItem::class);
    }

    public function indentPayment()
    {
        return $this->hasMany(IndentPayment::class);
    }

    public function indentComments()
    {
        return $this->hasMany(IndentComment::class);
    }

    public function indentApproveLogs()
    {
        return $this->hasMany(IndentApproveLog::class);
    }
}

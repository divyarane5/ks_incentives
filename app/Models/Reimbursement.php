<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reimbursement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_name',
        'project_name',
        'visit_attended_of_id',
        'source',
        'destination',
        'transport_mode',
        'amount',
        'settlement_amount',
        'comment',
        'status',
        'file_name',
        'file_path'
    ];

    protected $appends = ["reimbursement_code"];

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = is_object(Auth::user()) ? Auth::user()->id : 1;
        });
    }

    public function getReimbursementCodeAttribute() {
        return config('constants.REIMBURSEMENT_CODE_PREFIX').str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function visitAttendedOf()
    {
        return $this->belongsTo(User::class, 'visit_attended_of_id', 'id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = is_object(Auth::user()) ? Auth::user()->id : 1;
        });
    }

    protected $fillable = [
        'project_id',
        'developer_id',
        'client_name',
        'booking_date',
        'client_contact',
        'lead_source',
        'configuration',
        'flat_no',
        'wing',
        'tower',
        'booking_amount',
        'agreement_value',
        'passback',
        'additional_kicker',
        'registration_date',
        'sales_user_id',
        'remark',
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'sales_user_id');
    }
}

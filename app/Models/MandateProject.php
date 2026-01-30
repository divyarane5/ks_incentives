<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class MandateProject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_name',
        'brand_name',
        'location',
        'rera_number',
        'property_type',
        'business_unit_id',
        'threshold_percentage',
        'brokerage', // ✅ ADD
        'brokerage_criteria',
        'created_by',
    ];

    public function configurations()
    {
        return $this->hasMany(MandateProjectConfiguration::class);
    }
     public function ladders()
    {
        return $this->hasMany(MandateProjectLadder::class);
    } 
    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = is_object(Auth::user()) ? Auth::user()->id : 1;
        });
    }
    public function clientEnquiries()
    {
        return $this->hasMany(ClientEnquiry::class);
    }
    public function cp()
    {
        return $this->belongsTo(User::class, 'cp_id'); // column in mandate_projects
    }
}

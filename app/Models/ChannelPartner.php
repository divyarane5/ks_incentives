<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class ChannelPartner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'firm_name', 'owner_name', 'contact', 'rera_number',
        'operational_locations', 'office_locations',
        'sourcing_manager', 'acquisition_channel', 'property_type', 'created_by'
    ];

    protected $casts = [
        'operational_locations' => 'array',
        'office_locations' => 'array',
        'acquisition_channel' => 'array',
    ];

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::id() ?? 1;
        });
    }

    public function sourcingManager()
    {
        return $this->belongsTo(\App\Models\User::class, 'sourcing_manager');
    }
}

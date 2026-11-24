<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;


class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'developer_id',
        'base_brokerage_percent',
        'rera_number',
        'project_contacts',
        // KeyLeads details
        'category', 'launch_status', 'location', 'city', 'min_price', 'max_price',
        'possession_date', 'description', 'is_active',
    ];

    protected $casts = [
        'possession_date' => 'date',
    ];

    protected static function boot() { 
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = is_object(Auth::user()) ? Auth::user()->id : 1;
        });
    }

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    public function ladders()
    {
        return $this->hasMany(ProjectLadder::class);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /* ---------------- KeyLeads ---------------- */

    /** KeyLeads "builder" is the developer. */
    public function builder()
    {
        return $this->developer();
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function leads()
    {
        return $this->belongsToMany(Lead::class);
    }

    /** Active = status 1, as used everywhere in the back office. */
    public function getIsActiveAttribute(): bool
    {
        return (int) $this->status === 1;
    }

    public function setIsActiveAttribute($value): void
    {
        $this->attributes['status'] = $value ? 1 : 0;
    }

    public function scopeActive($query)
    {
        return $query->where($this->getTable().'.status', 1);
    }
}

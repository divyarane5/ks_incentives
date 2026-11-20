<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    protected $fillable = ['project_id', 'property_type_id', 'title', 'listing_for', 'bhk', 'price', 'carpet_area', 'area_unit', 'unit_number', 'location', 'city', 'status', 'owner_name', 'owner_phone', 'created_by_id'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    public function leads(): BelongsToMany
    {
        return $this->belongsToMany(Lead::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadEnquiry extends Model
{
    protected $fillable = ['lead_id', 'enquired_for', 'property_type_id', 'bhk', 'budget_min', 'budget_max', 'currency', 'locations', 'purpose', 'possession'];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }
}

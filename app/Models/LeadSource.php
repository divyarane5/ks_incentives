<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadSource extends Model
{
    protected $fillable = ['name', 'slug', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function subSources(): HasMany
    {
        return $this->hasMany(LeadSubSource::class, 'source_id')->orderBy('sort_order')->orderBy('name');
    }
}

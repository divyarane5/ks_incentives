<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyType extends Model
{
    protected $fillable = ['parent_id', 'name', 'sort_order'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(PropertyType::class, 'parent_id')->orderBy('sort_order');
    }
}

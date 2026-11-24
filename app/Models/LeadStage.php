<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadStage extends Model
{
    protected $fillable = ['parent_id', 'name', 'slug', 'color', 'outcome', 'schedule_type', 'is_default', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean', 'is_active' => 'boolean'];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(LeadStage::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(LeadStage::class, 'parent_id')->orderBy('sort_order');
    }

    public function isClosed(): bool
    {
        return $this->outcome !== 'open';
    }
}

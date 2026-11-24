<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentRule extends Model
{
    protected $fillable = ['name', 'source_id', 'project_id', 'user_ids', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['user_ids' => 'array', 'is_active' => 'boolean'];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'source_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function users()
    {
        return User::whereIn('id', $this->user_ids ?? [])->orderBy('name')->get(['id', 'name', 'status']);
    }
}

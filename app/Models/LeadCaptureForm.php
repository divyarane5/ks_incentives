<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LeadCaptureForm extends Model
{
    protected $fillable = ['name', 'source_id', 'project_id', 'owner_id', 'heading', 'thank_you_url', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'last_used_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(fn (self $form) => $form->token ??= Str::random(40));
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'source_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** The ready-made form page to link to or embed. */
    public function pageUrl(): string
    {
        return route('crm.capture.form', $this->token);
    }

    /** Where a website's own form or code sends leads. */
    public function postUrl(): string
    {
        return route('crm.capture.store', $this->token);
    }
}

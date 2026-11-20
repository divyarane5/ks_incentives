<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadImport extends Model
{
    protected $fillable = [
        'user_id', 'file_name', 'path', 'status', 'headers', 'preview', 'mapping', 'options',
        'total_rows', 'processed_rows', 'created_count', 're_enquiry_count', 'skipped_count', 'error_count',
        'problems', 'summary', 'message', 'started_at', 'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'headers' => 'array',
            'preview' => 'array',
            'mapping' => 'array',
            'options' => 'array',
            'problems' => 'array',
            'summary' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRunning(): bool
    {
        return in_array($this->status, ['queued', 'running'], true);
    }

    public function percent(): int
    {
        return $this->total_rows ? (int) floor($this->processed_rows * 100 / $this->total_rows) : 0;
    }
}

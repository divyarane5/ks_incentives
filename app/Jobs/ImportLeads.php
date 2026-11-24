<?php

namespace App\Jobs;

use App\Imports\LeadImporter;
use App\Models\LeadImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Imports one chunk of an uploaded sheet, then queues the next chunk.
 * Small chunks keep every job short, so big Leadrat exports never hit a timeout.
 */
class ImportLeads implements ShouldQueue
{
    use Queueable;

    public const CHUNK = 500;

    /** Never retry: a half-done chunk run twice would record its rows twice as re-enquiries. */
    public int $tries = 1;

    public int $timeout = 600;

    public function __construct(public int $importId, public int $start = 0) {}

    public function handle(): void
    {
        $import = LeadImport::find($this->importId);
        if (! $import || ! $import->isRunning()) {
            return;
        }
        if ($import->status === 'queued') {
            $import->forceFill(['status' => 'running', 'started_at' => now()])->save();
        }

        $more = (new LeadImporter($import))->run($this->start, self::CHUNK);

        if ($more) {
            self::dispatch($this->importId, $this->start + self::CHUNK);
        } else {
            $import->forceFill(['status' => 'done', 'finished_at' => now(), 'processed_rows' => $import->total_rows])->save();
        }
    }

    public function failed(?\Throwable $e): void
    {
        LeadImport::whereKey($this->importId)->update([
            'status' => 'failed',
            'finished_at' => now(),
            'message' => 'Stopped at row '.($this->start + 2).': '.str($e?->getMessage() ?? 'unknown error')->limit(300),
        ]);
    }
}

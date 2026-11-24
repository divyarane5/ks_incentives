<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LeadSubSource extends Model
{
    protected $fillable = ['source_id', 'name', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'source_id');
    }

    /**
     * The list's spelling of a sub source ("microsite " becomes "Microsite"). A name the list
     * does not have yet is added, so uploads, forms and Facebook never lose what they sent.
     */
    public static function canonical(?int $sourceId, ?string $name): ?string
    {
        $name = Str::limit(Str::squish((string) $name), 100, '');
        if ($name === '' || ! $sourceId) {
            return $name === '' ? null : $name;
        }

        $match = self::where('source_id', $sourceId)->get(['name'])
            ->first(fn ($s) => mb_strtolower($s->name) === mb_strtolower($name));

        return $match?->name ?? self::create(['source_id' => $sourceId, 'name' => $name])->name;
    }
}

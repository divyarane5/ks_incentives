<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** Who signs HR letters: name, designation, signature and stamp images, optionally for one company. */
class LetterSignatory extends Model
{
    protected $fillable = ['company', 'name', 'designation', 'signature', 'stamp', 'is_default', 'active'];

    protected $casts = ['is_default' => 'boolean', 'active' => 'boolean'];

    /** The signatory for a company: one set for that company, else the default, else any active one for all companies. */
    public static function forCompany(?string $company): ?self
    {
        if (! Schema::hasTable('letter_signatories')) {
            return null;
        }
        $all = static::where('active', true)->orderByDesc('is_default')->orderBy('id')->get();
        $c = mb_strtolower(trim((string) $company));

        return $all->first(fn ($s) => $s->company && mb_strtolower(trim($s->company)) === $c)
            ?? $all->first(fn ($s) => $s->is_default)
            ?? $all->first(fn ($s) => ! $s->company);
    }

    /** Image as a data URI, so the PDF maker can always read it. */
    public function dataUri(string $field): ?string
    {
        $path = $this->{$field};
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }
        $mime = Storage::disk('public')->mimeType($path) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode(Storage::disk('public')->get($path));
    }

    public function url(string $field): ?string
    {
        return $this->{$field} && Storage::disk('public')->exists($this->{$field}) ? Storage::disk('public')->url($this->{$field}) : null;
    }
}

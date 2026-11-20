<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as Query;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'serial_no', 'name', 'country_code', 'phone', 'alt_phone', 'email',
        'source_id', 'sub_source', 'campaign', 'stage_id', 'sub_stage_id', 'temperature',
        'owner_id', 'secondary_owner_id', 'assigned_by_id', 'assigned_at', 'picked_at',
        'next_follow_up_at', 'next_follow_up_type', 'last_contacted_at',
        'original_lead_id', 're_enquiry_count', 're_enquired_at', 'is_archived', 'legacy_id', 'created_by_id',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'picked_at' => 'datetime',
            'next_follow_up_at' => 'datetime',
            'last_contacted_at' => 'datetime',
            're_enquired_at' => 'datetime',
            'is_archived' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Lead $lead) {
            $lead->phone = self::normalizePhone($lead->phone);
            $lead->serial_no ??= self::newSerialNo();
        });
        static::saving(function (Lead $lead) {
            if ($lead->isDirty(['sub_source', 'source_id'])) {
                $lead->sub_source = LeadSubSource::canonical($lead->source_id, $lead->sub_source);
            }
        });
    }

    /** A short reference like L261112A3F09C, checked so bulk imports never clash. */
    public static function newSerialNo(): string
    {
        do {
            $serial = 'L'.now()->format('ymd').strtoupper(bin2hex(random_bytes(3)));
        } while (self::withTrashed()->where('serial_no', $serial)->exists());

        return $serial;
    }

    /** Keep only digits and drop a leading 0 or country code so duplicates match. */
    public static function normalizePhone(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }
        $digits = preg_replace('/\D+/', '', $phone);
        if (strlen($digits) > 10 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        }

        return ltrim($digits, '0') ?: $digits;
    }

    // Relationships

    public function source(): BelongsTo { return $this->belongsTo(LeadSource::class, 'source_id'); }
    public function stage(): BelongsTo { return $this->belongsTo(LeadStage::class, 'stage_id'); }
    public function subStage(): BelongsTo { return $this->belongsTo(LeadStage::class, 'sub_stage_id'); }
    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }
    public function secondaryOwner(): BelongsTo { return $this->belongsTo(User::class, 'secondary_owner_id'); }
    public function assignedBy(): BelongsTo { return $this->belongsTo(User::class, 'assigned_by_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by_id'); }
    public function originalLead(): BelongsTo { return $this->belongsTo(Lead::class, 'original_lead_id'); }
    public function enquiry(): HasOne { return $this->hasOne(LeadEnquiry::class); }
    public function projects(): BelongsToMany { return $this->belongsToMany(Project::class); }
    public function booking(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(Booking::class); }
    public function properties(): BelongsToMany { return $this->belongsToMany(Property::class); }
    public function activities(): HasMany { return $this->hasMany(LeadActivity::class)->latest('created_at'); }
    public function appointments(): HasMany { return $this->hasMany(Appointment::class)->latest('scheduled_at'); }
    public function callLogs(): HasMany { return $this->hasMany(CallLog::class)->latest('called_at'); }

    // Scopes

    /** Leads the given user may see: their own and their team's (admins see all). */
    public function scopeVisibleTo(Query $query, User $user): Query
    {
        if ($user->isAdmin()) {
            return $query;
        }
        $ids = $user->teamUserIds();
        $seesUnassigned = $user->role !== User::ROLE_SALES;

        return $query->where(function (Query $q) use ($ids, $seesUnassigned) {
            $q->whereIn('owner_id', $ids)->orWhereIn('secondary_owner_id', $ids);
            if ($seesUnassigned) {
                $q->orWhereNull('owner_id');
            }
        });
    }

    public function scopeActive(Query $query): Query
    {
        return $query->where('is_archived', false);
    }

    public function scopeOverdue(Query $query): Query
    {
        return $query->active()->where('next_follow_up_at', '<', now());
    }

    public function scopeDueToday(Query $query): Query
    {
        return $query->active()->whereBetween('next_follow_up_at', [now()->startOfDay(), now()->endOfDay()]);
    }

    /** Find an existing lead with the same phone number, for the duplicate check. */
    public static function findByPhone(string $phone): ?self
    {
        return self::where('phone', self::normalizePhone($phone))->whereNull('original_lead_id')->first();
    }
}

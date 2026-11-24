<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeReportingManagerHistory extends Model
{
    use HasFactory;

    protected $table = 'employee_reporting_manager_histories';

    protected $fillable = [
        'user_id',
        'reporting_manager_id',
        'effective_from',
        'effective_to',
        'changed_by',
        'remarks',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    /**
     * Record a reporting manager change for one employee, from a given date.
     * Closes the open record the day before (or on the same day, if it only just began)
     * and opens a new one. A null manager only closes the open record.
     */
    public static function record(User $user, ?int $managerId, $from = null, ?string $remarks = null): void
    {
        $from = \Carbon\Carbon::parse($from ?: now())->startOfDay();

        $open = static::where('user_id', $user->id)->whereNull('effective_to')->latest('effective_from')->first();
        if ($open && (int) $open->reporting_manager_id === (int) $managerId) {
            return; // nothing changed
        }

        static::where('user_id', $user->id)->whereNull('effective_to')->get()->each(function ($row) use ($from) {
            $end = $from->copy()->subDay();
            $row->effective_to = $end->lt($row->effective_from) ? $row->effective_from : $end;
            $row->save();
        });

        if ($managerId) {
            static::create([
                'user_id' => $user->id,
                'reporting_manager_id' => $managerId,
                'effective_from' => $from->toDateString(),
                'changed_by' => auth()->id(),
                'remarks' => $remarks,
            ]);
        }
    }

    /** Who this employee reported to on a given date (null if not recorded). */
    public static function managerOn(int $userId, $date): ?int
    {
        $d = \Carbon\Carbon::parse($date)->toDateString();

        return static::where('user_id', $userId)
            ->where('effective_from', '<=', $d)
            ->where(fn ($q) => $q->whereNull('effective_to')->orWhere('effective_to', '>=', $d))
            ->latest('effective_from')->value('reporting_manager_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // EmployeeReportingManagerHistory.php
    public function manager()
    {
        return $this->belongsTo(User::class, 'reporting_manager_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    public const STATUSES = ['pending' => 'Waiting', 'approved' => 'Approved', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled'];

    protected $fillable = ['user_id', 'leave_type_id', 'from_date', 'to_date', 'half_day', 'days', 'reason', 'status',
        'approver_id', 'decided_by', 'decided_at', 'decision_note', 'created_by'];

    protected $casts = ['from_date' => 'date', 'to_date' => 'date', 'decided_at' => 'datetime', 'days' => 'float'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function decider()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function getDatesLabelAttribute(): string
    {
        $f = $this->from_date;
        $t = $this->to_date;
        if ($f->equalTo($t)) {
            return $f->format('D, d M Y').($this->half_day ? ' ('.($this->half_day === 'first' ? 'first half' : 'second half').')' : '');
        }

        return $f->format($f->year === $t->year ? 'd M' : 'd M Y').' – '.$t->format('d M Y');
    }
}

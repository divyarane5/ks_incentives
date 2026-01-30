<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MandateBookingBrokerage extends Model
{
    protected $table = 'mandate_booking_brokerages';

    protected $guarded = [];

    protected $fillable = [
        'booking_id',
        'agreement_value',
        'total_paid',
        'payment_percent',
        'threshold_percentage',
        'current_due_percentage',
        'brokerage_percent',
        'brokerage_amount',
        'is_registered',
        'is_eligible',
        'eligibility_scenario',
        'eligibility_reason',
        'status',
        'bill_copy',
        'acceptance_copy',
        'eligible_at',
        'paid_at',
    ];

    public function booking()
    {
        return $this->belongsTo(MandateBooking::class, 'booking_id');
    }
    public function ledgers()
    {
        return $this->hasMany(
            MandateBookingBrokerageLedger::class,
            'brokerage_id'
        );
    }

    /**
     * Helper: how much brokerage already paid
     */
    public function paidAmount()
    {
        return $this->ledgers()
            ->where('status', 'paid')
            ->sum('brokerage_amount');
    }

    /**
     * Helper: balance brokerage
     */
    public function balanceAmount()
    {
        return $this->brokerage_amount - $this->paidAmount();
    }
}

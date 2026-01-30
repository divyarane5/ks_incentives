<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// App\Models\MandateBookingBrokerageLedger.php

class MandateBookingBrokerageLedger extends Model
{
    protected $table = 'mandate_booking_brokerage_ledgers';

    protected $fillable = [
        'booking_id',
        'brokerage_id',
        'party_type',
        'channel_partner_id',
        'brokerage_percent',
        'brokerage_amount',
        'entry_type',
        'calculation_type',
        'payment_mode',
        'reference_no',
        'payment_date',
        'status',
        'is_locked',
        'remark',
        'effective_from',
        'created_by',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'payment_date' => 'date',
        'effective_from' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(
            MandateBooking::class,
            'booking_id'
        );
    }

    public function brokerage()
    {
        return $this->belongsTo(
            MandateBookingBrokerage::class,
            'brokerage_id'
        );
    }

    public function channelPartner()
    {
        return $this->belongsTo(
            ChannelPartner::class,
            'channel_partner_id'
        );
    }
}

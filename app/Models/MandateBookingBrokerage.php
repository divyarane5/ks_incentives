<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MandateBookingBrokerage extends Model
{
    protected $table = 'mandate_booking_brokerages';

    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(MandateBooking::class, 'booking_id');
    }
}

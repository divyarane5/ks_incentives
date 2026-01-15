<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MandateBookingPayment extends Model
{
    protected $table = 'mandate_booking_payments';

    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(MandateBooking::class, 'booking_id');
    }
}

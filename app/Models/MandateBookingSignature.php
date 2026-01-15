<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MandateBookingSignature extends Model
{
    protected $table = 'mandate_booking_signatures';
    protected $guarded = [];

    public function signature()
    {
        return $this->hasOne(
            \App\Models\MandateBookingSignature::class,
            'booking_id'
        );
    }
}

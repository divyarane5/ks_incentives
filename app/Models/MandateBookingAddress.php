<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MandateBookingAddress extends Model
{
    protected $table = 'mandate_booking_addresses';

    protected $guarded = [];

    public function applicant()
    {
        return $this->belongsTo(
            MandateBookingApplicant::class,
            'applicant_id'
        );
    }
}
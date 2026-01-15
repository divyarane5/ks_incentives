<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MandateBooking extends Model
{
    protected $table = 'mandate_bookings';

    protected $fillable = [
        'project_id',
        'unit_no',
        'tower',
        'floor_no',
        'configuration',

        'applicant1_client_enquiry_id',
        'applicant1_name',
        'applicant1_mobile',
        'applicant1_email',
        'applicant1_pan',

        'applicant2_client_enquiry_id',
        'applicant2_name',
        'applicant2_mobile',
        'applicant2_email',
        'applicant2_pan',

        'channel_partner_id',
        'total_agreement_value',
        'booking_amount',
        'payment_mode',
        'payment_date',

        'status',
        'created_by',
    ];

    public function project()
    {
        return $this->belongsTo(MandateProject::class, 'project_id');
    }

    public function finance()
    {
        return $this->hasOne(MandateBookingFinance::class, 'booking_id');
    }

    public function applicants()
    {
        return $this->hasMany(MandateBookingApplicant::class, 'booking_id');
    }

    public function payments()
    {
        return $this->hasMany(MandateBookingPayment::class, 'booking_id');
    }

    public function brokerage()
    {
        return $this->hasOne(MandateBookingBrokerage::class, 'booking_id');
    }
    public function channel_partner()
    {
        return $this->belongsTo(\App\Models\ChannelPartner::class, 'channel_partner_id');
    }
    public function signature()
    {
        return $this->hasOne(
            \App\Models\MandateBookingSignature::class,
            'booking_id'
        );
    }

}

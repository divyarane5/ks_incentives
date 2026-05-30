<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingBrokeragePayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_id',
        'invoice_percent',
        'invoice_amount',
        'invoice_date',
        'invoice_file',
        'bank_received_amount',
        'bank_received_date',
        'status',
        'remarks'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
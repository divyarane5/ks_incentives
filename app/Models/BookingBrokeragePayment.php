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
        'invoice_number',
        'invoice_percent',
        'invoice_amount',
        'invoice_date',

        'bank_received_amount',
        'actual_receipt_amount',   // <-- add here
        'tds_amount',

        'cgst_percent',
        'cgst_amount',
        'sgst_percent',
        'sgst_amount',
        'total_gst_amount',

        'remarks',
        'status',

    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function billingEntity()
    {
        return $this->belongsTo(
            DeveloperBillingEntity::class,
            'developer_billing_entity_id'
        );
    }

    public function companyBank()
    {
        return $this->belongsTo(
            CompanyBankAccount::class,
            'company_bank_account_id'
        );
    }
}
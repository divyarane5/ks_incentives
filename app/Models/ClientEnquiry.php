<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientEnquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_name',
        'address',
        'pin_code',
        'contact_no',
        'alternate_no',
        'email',
        'profession',
        'company_name',
        'residential_status',
        'nri_country',
        'channel_partner_id',
        'sourcing_manager_id', // added
        'presales_id', // added
        'property_type',
        'budget',
        'purchase_purpose',
        'funding_source',
        'team_call_received',
        'source_of_visit',
        'reference_name',
        'reference_contact',
        'remarks', // added
        'closing_manager_id',
        'feedback',
        'created_by',
    ];

    protected $casts = [
        //'source_of_visit' => 'array',
        'team_call_received' => 'boolean',
    ];

    // 🔗 Relationships
    public function channelPartner()
    {
        return $this->belongsTo(ChannelPartner::class, 'channel_partner_id');
    }

    public function closingManager()
    {
        return $this->belongsTo(User::class, 'closing_manager_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function sourcingManager()
    {
        return $this->belongsTo(User::class, 'sourcing_manager_id');
    }
    public function presales()
    {
        return $this->belongsTo(User::class, 'presales_id'); 
        // Replace 'presales_manager_id' with the actual column name in your table
    }
    public function updates()
    {
        return $this->hasMany(ClientEnquiryUpdate::class, 'client_enquiry_id');
    }
    
}

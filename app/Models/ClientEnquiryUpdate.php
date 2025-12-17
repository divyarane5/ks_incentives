<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientEnquiryUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_enquiry_id',
        'user_id',
        'feedback',
        'revisit_scheduled',
        'revisit_done',
        'followup_date',
        'status'
    ];

    public function enquiry()
    {
        return $this->belongsTo(ClientEnquiry::class, 'client_enquiry_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // public function updates()
    // {
    //     return $this->hasMany(ClientEnquiryUpdate::class);
    // }
    
    
}

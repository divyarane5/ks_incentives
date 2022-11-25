<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoiningForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
                'candidate_id',
                'joining_date',
                'designation',
                'first_name',
                'last_name',
                'middle_name',
                'present_address',
                'permanent_address',
                'mobile',
                'email',
                'dob',
                'marital_status',
                'pan_number',
                'blood_group',
                'aadhar_number',
                'gender',
                'emergency_contact_name',
                'emergency_contact_relation',
                'emergency_contact_number',
                'bank_name',
                'branch_name',
                'account_number',
                'ifsc',
                'educational_details',
                'organizational_details',
                'family_details',
                'professional_details',
                'suffered_from_disease',
                'practitioner_details',
                'convicted_in_law',
                'photo'
            ];
}

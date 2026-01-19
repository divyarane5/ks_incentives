<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_code', 'entity', 'title', 'first_name', 'middle_name', 'last_name',
        'name', 'gender', 'photo', 'status', 'official_contact', 'personal_contact',
        'email', 'official_email', 'personal_email', 'department_id', 'designation_id',
        'role_id', 'reporting_manager_id', 'work_location_id', 'location_handled',
        'joining_date', 'confirm_date', 'leaving_date', 'exit_status', 
        'reason_for_leaving', 'fnf_status', 'current_ctc', 'monthly_basic', 
        'monthly_hra', 'special_allowance', 'conveyance_allowance', 
        'medical_reimbursement', 'professional_tax', 'pf_employer', 
        'pf_employee', 'net_deductions', 'net_salary', 'pf_status', 
        'pf_joining_date', 'uan_number', 'bank_name', 'ifsc_code', 
        'bank_account_number', 'dob', 'age', 'birthday_month', 'blood_group',
        'communication_address', 'permanent_address', 'languages_known', 
        'education_qualification', 'marital_status', 'marriage_date', 
        'spouse_name', 'parents_contact', 'emergency_contact_name', 
        'emergency_contact_relationship', 'emergency_contact_number', 
        'pan_no', 'aadhar_no', 'laptop_desktop', 'company_phone', 
        'company_sim', 'work_off', 'additional_comments', 'created_by', 
        'password', 'remember_token', 'last_login','business_unit_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = is_object(Auth::user()) ? Auth::user()->id : 1;
        });
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'work_location_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id');
    }

    public function reportingTo()
    {
        return $this->belongsTo(User::class, 'reporting_manager_id', 'id');
    }

    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class, 'business_unit_id');
    }

    // Users reporting to this user (team members)
    public function teamMembers()
    {
        return $this->hasMany(User::class, 'reporting_manager_id');
    }

    // User's own channel partners
    public function ownChannelPartners()
    {
        return $this->hasMany(ChannelPartner::class, 'sourcing_manager');
    }

    public function salaries()
    {
        return $this->hasMany(UserSalary::class);
    }

}

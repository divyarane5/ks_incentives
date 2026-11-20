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
        'name', 'gender', 'photo', 'status',

        /* Contact */
        'official_contact', 'personal_contact',
        'email', 'official_email', 'personal_email',

        /* Employment */
        'department_id', 'designation_id', 'role_id',
        'reporting_manager_id',
        'work_location_id', 'location_handled',
        'joining_date', 'confirm_date',
        'employment_status',            // ✅ ADD
        'probation_period_days',        // ✅ ADD
        'leaving_date',
        'notice_period_days',           // ✅ ADD
        'exit_status',
        'reason_for_leaving',
        'fnf_status',

        /* Salary */
        'annual_ctc',                   // ✅ ADD
        'current_ctc',
        'monthly_basic',
        'monthly_hra',
        'special_allowance',
        'conveyance_allowance',
        'medical_reimbursement',
        'professional_tax',
        'pf_employer',
        'pf_employee',
        'net_deductions',
        'net_salary',

        /* Statutory */
        'pf_status',
        'pf_joining_date',
        'uan_number',

        /* Banking */
        'bank_name',
        'bank_account_name',
        'bank_branch_name',
        'bank_account_type',
        'ifsc_code',
        'bank_account_number',

        /* Personal */
        'dob',
        'age',
        'birthday_month',
        'blood_group',
        'communication_address',
        'permanent_address',
        'languages_known',
        'education_qualification',
        'marital_status',
        'marriage_date',
        'spouse_name',
        'parents_contact',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_number',
        'pan_no',
        'aadhar_no',

        /* Assets & Misc */
        'laptop_desktop',
        'company_phone',
        'company_sim',
        'work_off',
        'additional_comments',

        /* Meta */
        'business_unit_id',
        'created_by',
        'password',
        'remember_token',
        'last_login',
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

    // ========================
    // Reporting Manager Accessor
    // ========================
    public function reportingManagerHistories()
    {
        return $this->hasMany(EmployeeReportingManagerHistory::class);
    }

    public function latestReportingManagerHistory()
    {
        return $this->hasOne(EmployeeReportingManagerHistory::class)->latestOfMany();
    }

    /**
     * Get the current reporting manager (from history table or fallback to users.reporting_manager_id)
     */
    public function getCurrentManagerAttribute()
    {
        // Check history first
        $history = $this->reportingManagerHistories()
            ->whereNull('effective_to')
            ->latest('effective_from')
            ->first();

        if ($history && $history->manager) {
            return $history->manager;
        }

        // Fallback to user.reporting_manager_id
        return $this->reporting_manager_id ? User::find($this->reporting_manager_id) : null;
    }

    // ========================
    // Salary Relations
    // ========================
    public function salaryHistories()
    {
        return $this->hasMany(EmployeeSalaryHistory::class);
    }

    public function latestSalaryHistory()
    {
        return $this->hasOne(EmployeeSalaryHistory::class)->latestOfMany();
    }

    // ========================
    // Exit Relations
    // ========================
    public function exitHistories()
    {
        return $this->hasMany(EmployeeExitHistory::class);
    }

    public function latestExitHistory()
    {
        return $this->hasOne(EmployeeExitHistory::class)->latestOfMany();
    }

    // User.php

    
    public function previousEmploymentDocuments()
    {
        return $this->hasMany(\App\Models\EmployeeDocument::class, 'user_id')
                    ->where('document_type', 'previous_employment');
    }
    public function currentReportingManagerHistory()
    {
        return $this->hasOne(EmployeeReportingManagerHistory::class)
                    ->whereNull('effective_to')
                    ->latestOfMany();
    }
    public function reportingManager()
    {
        return $this->belongsTo(User::class, 'reporting_manager_id');
    }
    public function subordinates()
    {
        return $this->hasMany(User::class, 'reporting_manager_id');
    }
    public function subordinatesRecursive()
    {
        return $this->subordinates()->with('subordinatesRecursive');
    }
    public function getManagerLevel($level = 1)
    {
        $manager = $this;

        for ($i = 0; $i < $level; $i++) {
            if (!$manager || !$manager->reportingManager) {
                return null;
            }

            $manager = $manager->reportingManager;
        }

        return $manager;
    }

    /* =====================================================================
     * KeyLeads (CRM) bridge
     *
     * KeyLeads thinks in four levels: admin, general manager, manager, sales.
     * They come from the HR roles here, so there is one employee list:
     * Superadmin/Admin = admin, CH and Sr. TL = general manager, TL = manager,
     * FOS = sales. Others only if given the KeyLeads module (they act as admin).
     * ===================================================================== */

    public const ROLE_ADMIN = 'admin';
    public const ROLE_GENERAL_MANAGER = 'general_manager';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_SALES = 'sales';

    /** HR role name => KeyLeads level, strongest first. */
    public const CRM_ROLE_MAP = [
        'Superadmin' => self::ROLE_ADMIN,
        'Admin' => self::ROLE_ADMIN,
        'CH' => self::ROLE_GENERAL_MANAGER,
        'Sr. TL' => self::ROLE_GENERAL_MANAGER,
        'TL' => self::ROLE_MANAGER,
        'FOS' => self::ROLE_SALES,
    ];

    private ?string $crmRoleCache = null;

    private bool $crmRoleLoaded = false;

    /** KeyLeads level for this person, or null if they don't use KeyLeads. */
    public function crmRole(): ?string
    {
        if (! $this->crmRoleLoaded) {
            $names = $this->getRoleNames()->all();
            $this->crmRoleCache = null;
            foreach (self::CRM_ROLE_MAP as $hrRole => $level) {
                if (in_array($hrRole, $names, true)) {
                    $this->crmRoleCache = $level;
                    break;
                }
            }
            // Anyone else given KeyLeads (e.g. Marketing) works on all leads, like an admin.
            if ($this->crmRoleCache === null && $this->can('module-keyleads')) {
                $this->crmRoleCache = self::ROLE_ADMIN;
            }
            $this->crmRoleLoaded = true;
        }

        return $this->crmRoleCache;
    }

    /** KeyLeads code reads $user->role. */
    public function getRoleAttribute(): ?string
    {
        return $this->crmRole();
    }

    /** KeyLeads is a RealtySuite module: the person needs the module and a KeyLeads level. */
    public function usesCrm(): bool
    {
        return $this->can('module-keyleads') && $this->crmRole() !== null;
    }

    public function isAdmin(): bool
    {
        return $this->crmRole() === self::ROLE_ADMIN;
    }

    public function getIsActiveAttribute(): bool
    {
        return strcasecmp((string) $this->status, 'Active') === 0;
    }

    /** Phone used for sign-in, WhatsApp {my_phone} and Excel owner matching. */
    public function getPhoneAttribute(): ?string
    {
        return $this->official_contact ?: ($this->personal_contact ?: null);
    }

    public function scopeActive($query)
    {
        return $query->where($this->getTable().'.status', 'Active');
    }

    /** People who use KeyLeads, optionally only some levels. */
    public function scopeCrmUsers($query, ?array $levels = null)
    {
        $hrRoles = array_keys(array_filter(self::CRM_ROLE_MAP, fn ($l) => $levels === null || in_array($l, $levels, true)));

        return $query->whereHas('roles', fn ($q) => $q->whereIn('name', $hrRoles));
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'reporting_manager_id');
    }

    public function reportees()
    {
        return $this->hasMany(User::class, 'reporting_manager_id');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'owner_id');
    }

    /**
     * This user plus everyone below them in the reporting tree.
     * A manager sees their own leads and their whole team's.
     *
     * @return array<int>
     */
    public function teamUserIds(): array
    {
        $ids = [$this->id];
        $frontier = [$this->id];

        while ($frontier) {
            $frontier = User::whereIn('reporting_manager_id', $frontier)
                ->whereNotIn('id', $ids)
                ->pluck('id')
                ->all();
            $ids = array_merge($ids, $frontier);
        }

        return $ids;
    }

    /** Active people this user may hand leads to: everyone in KeyLeads for admins, otherwise their own team. */
    public static function assignableBy(User $user)
    {
        $q = self::active()->crmUsers()->orderBy('name');
        if (! $user->isAdmin()) {
            $q->whereIn('id', $user->teamUserIds());
        }

        return $q->get(['id', 'name']);
    }
}

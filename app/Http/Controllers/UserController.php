<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfileRequest;
use App\Http\Requests\UserRequest;
use App\Imports\ImportUser;
use App\Imports\UpdateUserReporting;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Department;
use App\Models\BusinessUnit;
use App\Models\Designation;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DB;
use DataTables;
use App\Helpers\ExcelSanitizer;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->middleware('permission:user-view', ['only' => ['index']]);
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
        $this->middleware('permission:user-org-tree-view')->only(['orgTree']);
        $this->middleware('permission:user-import')->only(['importUser','showImportForm']);
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $filters = array_filter($request->only([
                'department_id',
                'designation_id',
                'role_id',
                'status',
                'employment_status',
                'business_unit_id',
                'reporting_manager_id'
            ]));

            $users = $this->userRepository->getUsers($filters);

            return DataTables::of($users)
                // One "person" cell: photo, name, designation, code and a tap-to-call number (used on phones too).
                ->addColumn('person', function ($row) {
                    $fmt = \App\Support\PeopleFormat::class;
                    $name = trim($row->first_name.' '.$row->last_name) ?: $row->name;
                    $avatar = $row->photo
                        ? '<img class="ul-av" src="'.e(asset('storage/'.$row->photo)).'" alt="">'
                        : '<span class="ul-av ul-ini">'.e($fmt::initials($name)).'</span>';
                    $phone = $row->official_contact ?: $row->personal_contact;
                    $intl = $fmt::intlPhone($phone);
                    $sub = e($row->designation->name ?? 'No designation').($row->employee_code ? ' · '.e($row->employee_code) : '');
                    $meta = e(trim(($row->department->name ?? '').(($row->department->name ?? null) && ($row->location->name ?? null) ? ' · ' : '').($row->location->name ?? '')));
                    $cls = ['Active' => 'ok', 'Exited' => 'bad'][$row->status] ?? 'warn';
                    $chips = '<span class="kp-chip '.$cls.'">'.e($row->status ?: '—').'</span>';
                    if (strtolower((string) $row->employment_status) === 'probation') {
                        $chips .= ' <span class="kp-chip warn">Probation</span>';
                    }

                    return '<div class="ul-person">'.$avatar.'<div class="ul-txt">'
                        .'<a class="ul-name" href="'.route('users.show', $row->id).'">'.e($name).'</a>'
                        .'<div class="ul-sub">'.$sub.'</div>'
                        .($meta !== '' ? '<div class="ul-meta">'.$meta.'</div>' : '')
                        .'<div class="ul-chips">'.$chips.'</div>'
                        .'</div>'
                        .($intl ? '<a class="ul-call" href="tel:+'.$intl.'" title="Call '.e($phone).'"><i class="bx bx-phone"></i></a>' : '')
                        .'</div>';
                })
                ->addColumn('status_badge', function ($row) {
                    $cls = ['Active' => 'bg-label-success', 'Exited' => 'bg-label-secondary', 'Inactive' => 'bg-label-warning'][$row->status] ?? 'bg-label-secondary';
                    return '<span class="badge '.$cls.'">'.e($row->status ?: '-').'</span>';
                })
                ->addColumn('role', function ($row) {
                        // If Spatie roles exist, display them
                        if ($row->roles && $row->roles->count()) {
                            return $row->roles->pluck('name')->join(', ');
                        }

                        // Else fallback to role_id column (for legacy data)
                        if ($row->role_id) {
                            return \Spatie\Permission\Models\Role::find($row->role_id)->name ?? '';
                        }

                        return '';
                    })
                ->addColumn('designation', function($row) {
                    return $row->designation->name ?? '';
                })
                ->addColumn('department', function($row) {
                    return $row->department->name ?? '';
                })
                ->addColumn('location', function($row) {
                    return $row->location->name ?? '';
                })
                ->addColumn('action', function ($row) {
                    $slug = Str::slug(trim($row->first_name.' '.$row->last_name));
                    $actions = '<a class="dropdown-item" href="'.route('users.show', $row->id).'">
                                    <i class="bx bx-show me-1"></i> View
                                </a>';
                    if ($slug !== '') {
                        $actions .= '<a class="dropdown-item" target="_blank" href="'.route('users.card', $slug).'">
                                    <i class="bx bx-show me-1"></i> Card
                                </a>';
                    }
                    if (auth()->user()->can('user-edit')) {
                        $actions .= '<a class="dropdown-item" href="'.route('users.edit', $row->id).'">
                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>';
                    }

                    if (auth()->user()->can('user-delete')) {
                        $actions .= '<button class="dropdown-item" type="button" onclick="deleteUser('.$row->id.')">
                                        <i class="bx bx-trash me-1"></i> Delete
                                    </button>
                                    <form id="delete-form-'.$row->id.'" action="'.route('users.destroy', $row->id).'" method="POST" class="d-none">'
                                    .csrf_field()
                                    .method_field('DELETE').'
                                    </form>';
                    }

                    // if (auth()->user()->can('configuration-view')) {
                    //     $actions .= '<a class="dropdown-item" href="'.route('indent_configuration.index').'?user_id='.$row->id.'">
                    //                     <i class="bx bx-list-ul me-1"></i> Indent Configuration
                    //                 </a>';
                    // }
                    // ✅ NEW: Add Salary / View Salary
                    if(auth()->user()->can('user-salary-view')) {
                        $actions .= '<a class="dropdown-item" href="'.route('users.salary.index', $row->id).'">
                            <i class="bx bx-wallet me-1"></i> Salary
                        </a>';
                    }
                    return '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">'.$actions.'</div>
                            </div>';
                })
                ->rawColumns(['action', 'status_badge', 'person'])
                ->make(true);
        }
        // Only people who actually have someone reporting to them.
        $reportingUsers = User::whereIn('id', User::whereNotNull('reporting_manager_id')->select('reporting_manager_id'))
            ->orderByRaw("status = 'Active' desc")->orderBy('name')
            ->get(['id', 'name', 'employee_code', 'status']);
        $locations = Location::select(['id', 'name'])->orderBy('name')->get();
        $departments = Department::select(['id', 'name'])->orderBy('name')->get();
        $designations = Designation::select(['id', 'name'])->orderBy('name')->get();
        $roles = Role::select(['id', 'name'])->orderBy('name')->get();
        $businessUnits = BusinessUnit::where('status', 1)->pluck('name', 'id');
        // Head-count tiles at the top of the list.
        $counts = [
            'all' => User::count(),
            'active' => User::where('status', 'Active')->count(),
            'probation' => User::where('status', 'Active')->where('employment_status', 'probation')->count(),
            'joined' => User::where('status', 'Active')->where('joining_date', '>=', now()->startOfMonth()->subMonths(2))->count(),
            'exited' => User::where('status', 'Exited')->count(),
            'inactive' => User::where('status', 'Inactive')->count(),
        ];
        return view('users.index', compact('locations', 'departments', 'designations', 'roles','businessUnits','reportingUsers','counts'));
    }



    public function create()
    {
        $locations = Location::select(['id', 'name'])->orderBy('name')->get();
        $departments = Department::select(['id', 'name'])->orderBy('name')->get();
        $designations = Designation::select(['id', 'name'])->orderBy('name')->get();
        $reportingUsers = User::select(['id', 'name'])->orderBy('name')->get();
        $roles = Role::select(['id', 'name'])->orderBy('name')->get();
        $businessUnits = BusinessUnit::where('status', 1)->pluck('name', 'id');
        return view('users.create', compact('locations', 'departments', 'designations', 'reportingUsers', 'roles','businessUnits'));
    }

   public function store(Request $request)
    {
        // ✅ Validate using custom helper
        $errors = validateUserForm($request);
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        DB::transaction(function() use ($request) {

            // -------------------------
            // Handle file uploads
            // -------------------------
            $photoPath = $request->hasFile('photo') ? $request->file('photo')->store('user_photos', 'public') : null;
            $offerLetterPath = $request->hasFile('offer_letter_file') ? $request->file('offer_letter_file')->store('user_documents', 'public') : null;
            $joiningLetterPath = $request->hasFile('joining_letter_file') ? $request->file('joining_letter_file')->store('user_documents', 'public') : null;

            $previousDocs = [];
            if ($request->hasFile('previous_documents')) {
                foreach ($request->file('previous_documents') as $file) {
                    $previousDocs[] = $file->store('user_documents', 'public');
                }
            }

            // -------------------------
            // Calculate salary components
            // -------------------------
            $annualCtc = floatval($request->annual_ctc ?? 0);
            $monthlyCtc = $annualCtc / 12;

            $monthly_basic = $monthlyCtc * 0.5;
            $monthly_hra = $monthly_basic * 0.5;
            $special_allowance = $monthlyCtc * 0.1;
            $conveyance_allowance = $monthlyCtc * 0.1;
            $medical_reimbursement = $monthlyCtc * 0.05;

            $pfEmployer = $request->pf_status == 1 ? 1800 : 0;
            $pfEmployee = $request->pf_status == 1 ? 1800 : 0;
            $profTax = 200;

            $deductions = $pfEmployer + $pfEmployee + $profTax;

            $net_salary = $monthlyCtc - $deductions;

            // -------------------------
            // Create User
            // -------------------------
            $user = new \App\Models\User();
            $user->employee_code = $request->employee_code;
            $user->entity = $request->entity;
            $user->title = $request->title;
            $user->first_name = $request->first_name;
            $user->middle_name = $request->middle_name;
            $user->last_name = $request->last_name;
            $user->name = trim($request->title.' '.$request->first_name.' '.$request->middle_name.' '.$request->last_name);
            $user->gender = $request->gender;
            $user->photo = $photoPath;
            $user->status = $request->status ?? 'Active';

            // Business unit
            $user->business_unit_id = $request->business_unit_id ?: null;

            // Contact info
            $user->official_contact = $request->official_contact;
            $user->personal_contact = $request->personal_contact;
            $user->official_email = $request->official_email;
            $user->personal_email = $request->personal_email;
            $user->email = $request->official_email; // login email

            // Employment details
            $user->department_id = $request->department_id;
            $user->designation_id = $request->designation_id;
            $user->role_id = $request->role_id;
            $user->reporting_manager_id = $request->reporting_manager_id;
            $user->location_handled = $request->location_handled;
            $user->work_location_id = $request->work_location_id;
            $user->joining_date = $request->joining_date;
            $user->probation_period_days = $request->probation_period_days ?? null;

            // ✅ Calculate confirmation date from joining + probation
            $user->confirm_date = $request->probation_period_days 
                ? \Carbon\Carbon::parse($request->joining_date)->addDays((int) $request->probation_period_days)
                : $request->confirm_date;

            // Employment status (default probation)
            $user->employment_status = $request->employment_status ?? 'probation';
            $user->leaving_date = $request->leaving_date;
            $user->notice_period_days = $request->notice_period_days ?? null;
            $user->exit_status = $request->exit_status;
            $user->reason_for_leaving = $request->reason_for_leaving;
            $user->fnf_status = $request->fnf_status;

            // Salary & Compensation
            $user->annual_ctc = $request->annual_ctc;
            $user->current_ctc = $monthlyCtc;
            $user->monthly_basic = $monthly_basic;
            $user->monthly_hra = $monthly_hra;
            $user->special_allowance = $special_allowance;
            $user->conveyance_allowance = $conveyance_allowance;
            $user->medical_reimbursement = $medical_reimbursement;
            $user->professional_tax = $profTax;
            $user->pf_employer = $pfEmployer;
            $user->pf_employee = $pfEmployee;
            $user->net_deductions = $deductions;
            $user->net_salary = $net_salary;

            // Statutory & Banking
            // Statutory & Banking
            $user->pf_status = (int) $request->pf_status;

            if ($user->pf_status == 1) {

                $user->pf_employee = 1800;
                $user->pf_employer = 1800;

            } else {

                $user->pf_employee = 0;
                $user->pf_employer = 0;
            }
            $user->pf_joining_date = $request->pf_joining_date;
            $user->uan_number = $request->uan_number;
            $user->bank_name = $request->bank_name;
            $user->bank_account_name = $request->bank_account_name;
            $user->bank_branch_name = $request->bank_branch_name;
            $user->bank_account_type = $request->bank_account_type;
            $user->ifsc_code = $request->ifsc_code;
            $user->bank_account_number = $request->bank_account_number;

            // Documents
            $user->offer_letter_sent = $request->offer_letter_sent ?? 0;
            $user->offer_letter_acknowledged = $request->offer_letter_acknowledged ?? 0;
            $user->offer_letter_file = $offerLetterPath;

            $user->joining_letter_sent = $request->joining_letter_sent ?? 0;
            $user->joining_letter_acknowledged = $request->joining_letter_acknowledged ?? 0;
            $user->joining_letter_file = $joiningLetterPath;

            // Personal & Emergency
            $personal_fields = [
                'dob','blood_group','communication_address','permanent_address',
                'languages_known','education_qualification','marital_status','marriage_date',
                'spouse_name','parents_contact','emergency_contact_name','emergency_contact_relationship',
                'emergency_contact_number','pan_no','aadhar_no'
            ];
            foreach($personal_fields as $field){
                $user->$field = $request->$field;
            }

            // Assets & Misc
            $user->work_off = $request->work_off;
            $user->additional_comments = $request->additional_comments;

            // Default password
            $user->password = \Hash::make('Welcome@123');

            $user->save();

            // Give the chosen role (permissions and modules come from it).
            if ($request->filled('role_id')) {
                $user->syncRoles([Role::findById((int) $request->role_id, 'web')]);
            }

            // -------------------------
            // Previous Employment Documents
            // -------------------------
            if(!empty($previousDocs)){
                foreach($previousDocs as $path){
                    \App\Models\EmployeeDocument::create([
                        'user_id' => $user->id,
                        'file_path' => $path,
                        'document_type' => 'previous_employment',
                    ]);
                }
            }

            // -------------------------
            // Salary History
            // -------------------------
            \App\Models\EmployeeSalaryHistory::create([
                'user_id' => $user->id,
                'annual_ctc' => $user->annual_ctc,
                'monthly_basic' => $user->monthly_basic,
                'monthly_hra' => $user->monthly_hra,
                'special_allowance' => $user->special_allowance,
                'conveyance_allowance' => $user->conveyance_allowance,
                'medical_reimbursement' => $user->medical_reimbursement,
                'professional_tax' => $user->professional_tax,
                'pf_employer' => $user->pf_employer,
                'pf_employee' => $user->pf_employee,
                'net_deductions' => $user->net_deductions,
                'net_salary' => $user->net_salary,
                'effective_from' => now(),
                'changed_by' => auth()->id(),
            ]);

            // -------------------------
            // Reporting Manager History
            // -------------------------
            if($user->reporting_manager_id){
                // New joiner: their first manager applies from the joining date.
                \App\Models\EmployeeReportingManagerHistory::record(
                    $user, (int) $user->reporting_manager_id, $user->joining_date ?: now(), 'On joining'
                );
            }

            // -------------------------
            // Exit History
            // -------------------------
            if($user->leaving_date){
                \App\Models\EmployeeExitHistory::create([
                    'user_id' => $user->id,
                    'leaving_date' => $user->leaving_date,
                    'notice_period_days' => $user->notice_period_days,
                    'exit_status' => $user->exit_status,
                    'reason_for_leaving' => $user->reason_for_leaving,
                    'created_by' => auth()->id(),
                ]);
            }

        }); // End Transaction

        return redirect()->route('users.index')->with('success','User created successfully!');
    }


    public function edit($id)
    {
        $user = User::with([
            'latestSalaryHistory',
            'latestReportingManagerHistory',
            'latestExitHistory',
        ])->findOrFail($id);
         // $user = User::findOrFail($id);
        $departments = Department::all();
       // $designations = Designation::all();
        $designations = Designation::select(['id', 'name'])->orderBy('name')->get();
        $roles = Role::all();
        // Active people (plus the current manager, even if they have left), A-Z.
        $reportingUsers = User::where(fn ($q) => $q->where('status', 'Active')->orWhere('id', $user->reporting_manager_id))
            ->where('id', '!=', $user->id)->orderBy('name')->get();
        $locations = Location::all();
        $businessUnits = BusinessUnit::where('status', 1)->pluck('name', 'id');
        $users = User::where('id', '!=', $id)->get(); // for reporting user dropdown
        return view('users.edit', compact(
            'user',
            'departments',
            'designations',
            'roles',
            'locations',
            'reportingUsers',
            'businessUnits'
        ));
    }

    public function update(UserRequest $request, $id)
    {
        // echo "<pre>"; 
        // print_r($request->all()); exit;
        $user = User::findOrFail($id);

        DB::transaction(function () use ($request, $user) {

            /* =========================
            | FILE UPLOADS
            ========================= */

            // Photo
            if ($request->hasFile('photo')) {
                if ($user->photo && file_exists(storage_path('app/public/' . $user->photo))) {
                    unlink(storage_path('app/public/' . $user->photo));
                }
                $user->photo = $request->file('photo')->store('user_photos', 'public');
            }

            // Offer Letter
            if ($request->hasFile('offer_letter_file')) {
                if ($user->offer_letter_file && file_exists(storage_path('app/public/' . $user->offer_letter_file))) {
                    unlink(storage_path('app/public/' . $user->offer_letter_file));
                }
                $user->offer_letter_file = $request->file('offer_letter_file')->store('user_documents', 'public');
            }

            // Joining Letter
            if ($request->hasFile('joining_letter_file')) {
                if ($user->joining_letter_file && file_exists(storage_path('app/public/' . $user->joining_letter_file))) {
                    unlink(storage_path('app/public/' . $user->joining_letter_file));
                }
                $user->joining_letter_file = $request->file('joining_letter_file')->store('user_documents', 'public');
            }

            /* =========================
            | BASIC DATA UPDATE
            ========================= */
            $user->fill($request->except([
                'photo',
                'offer_letter_file',
                'joining_letter_file',
                'previous_documents',
                'salary_effective_from',
                'salary_change_reason',
                'manager_effective_from',
                'manager_change_note',
            ]));

            // Full Name
            $user->name = trim(
                $request->title . ' ' .
                $request->first_name . ' ' .
                $request->middle_name . ' ' .
                $request->last_name
            );

            /* =========================
            | EMPLOYMENT LOGIC
            ========================= */

            // Official email is also the login email: keep them together when it changes.
            if ($user->isDirty('official_email') && $user->official_email
                && ! User::where('email', $user->official_email)->where('id', '!=', $user->id)->exists()) {
                $user->email = $user->official_email;
            }

            // Normalize employment status
            if ($request->filled('employment_status')) {
                $user->employment_status = strtolower($request->employment_status);
            }

            // Confirmation date
            if (
                $user->isDirty('probation_period_days') ||
                $user->isDirty('joining_date')
            ) {
                $user->confirm_date = \Carbon\Carbon::parse($user->joining_date)
                    ->addDays((int) $user->probation_period_days);
            }

            // PF status
            // PF status
            $user->pf_status = (int) $request->pf_status;

            if ($user->pf_status == 1) {

                $user->pf_employee = 1800;
                $user->pf_employer = 1800;

            } else {

                $user->pf_employee = 0;
                $user->pf_employer = 0;
            }

            // 🔐 SAVE FIRST (important for wasChanged)
            $user->save();

            /* =========================
            | PREVIOUS EMPLOYMENT DOCS
            ========================= */
            if ($request->hasFile('previous_documents')) {
                foreach ($request->file('previous_documents') as $file) {
                    \App\Models\EmployeeDocument::create([
                        'user_id' => $user->id,
                        'file_path' => $file->store('user_documents', 'public'),
                        'document_type' => 'previous_employment',
                    ]);
                }
            }

            /* =========================
            | SALARY HISTORY
            ========================= */
            if ($user->wasChanged('annual_ctc')) {
                // HR can say from when the new CTC applies and why (defaults: today, "Revision").
                $salaryFrom = $request->input('salary_effective_from') ?: now()->toDateString();
                $salaryReason = trim((string) $request->input('salary_change_reason')) ?: 'Revision';
                \App\Models\EmployeeSalaryHistory::create([
                    'user_id' => $user->id,
                    'annual_ctc' => $user->annual_ctc,
                    'monthly_basic' => $user->monthly_basic,
                    'monthly_hra' => $user->monthly_hra,
                    'special_allowance' => $user->special_allowance,
                    'conveyance_allowance' => $user->conveyance_allowance,
                    'medical_reimbursement' => $user->medical_reimbursement,
                    'professional_tax' => $user->professional_tax,
                    'pf_employer' => $user->pf_employer,
                    'pf_employee' => $user->pf_employee,
                    'net_deductions' => $user->net_deductions,
                    'net_salary' => $user->net_salary,
                    'change_reason' => mb_substr($salaryReason, 0, 190),
                    'effective_from' => $salaryFrom,
                    'changed_by' => auth()->id(),
                ]);
            }

            /* =========================
            | REPORTING MANAGER HISTORY
            ========================= */
            if ($user->wasChanged('reporting_manager_id')) {
                // HR can say from which date the new manager applies (defaults to today).
                \App\Models\EmployeeReportingManagerHistory::record(
                    $user,
                    $user->reporting_manager_id ? (int) $user->reporting_manager_id : null,
                    $request->input('manager_effective_from') ?: now(),
                    $request->input('manager_change_note')
                );
            }


            /* =========================
            | EXIT HISTORY
            ========================= */
            if (
                $user->wasChanged('leaving_date') ||
                $user->wasChanged('exit_status') ||
                $user->wasChanged('notice_period_days')
            ) {

                \App\Models\EmployeeExitHistory::create([
                    'user_id'      => $user->id,
                    'exit_date'    => $user->leaving_date,
                    'exit_type'    => $user->exit_status,
                    'exit_reason'  => $user->reason_for_leaving,
                    'remarks'      => 'Notice Period: '.$user->notice_period_days.' days',
                    'approved_by'  => auth()->id(),
                    'is_rehirable' => 1,
                ]);
            }

            /* =========================
            | ROLE SYNC
            ========================= */
            if ($request->filled('role_id')) {
                // role_id is a number: look the role up by id (a plain "4" is read as a role name).
                $role = Role::findById((int) $request->role_id, 'web');
                $user->syncRoles([$role]);
            }
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        User::where('id', $id)->delete();
        return redirect()->route('users.index')->with('success', 'User Deleted Successfully');
    }

    public function account()
    {
        $user = auth()->user()->load([
            'location',
            'department',
            'designation',
            'businessUnit',
            'latestReportingManagerHistory.manager'
        ]);
        

        return view('users.account', compact('user'));
    }
    public function updateProfile(UserProfileRequest $request)
    {
        $user = auth()->user();
        $user->name = $request->name;
        $user->dob = $request->dob;
        $user->gender = $request->gender;
        $user->save();

        if ($request->has('photo')) {
            if ($user->photo && file_exists(storage_path('app/'.$user->photo))) {
                unlink(storage_path('app/'.$user->photo));
            }
            $user->photo = uploadFile($request->file('photo'), config('uploadfilepath.USER_PROFILE_PHOTO'));
            $user->save();
        }

        return redirect()->route('account')->with('success', 'Profile Updated Successfully');
    }

    
    public function show($id)
    {
        // HR (user-view) can open anyone; everyone else only their own profile.
        $me = auth()->user();
        abort_unless($me && ($me->can('user-view') || (int) $me->id === (int) $id), 403);

        $user = User::with([
            'department',
            'designation',
            'location',
            'businessUnit',
            'roles',
            'salaryHistories',
            'latestExitHistory',
            'exitHistories',
            'previousEmploymentDocuments',
            'reportingManager.designation',
            'teamMembers.designation',
            'reportingManagerHistories.manager',
            'reportingManagerHistories.changedBy',
            'reporteeHistories.employee',
            'currentReportingManagerHistory.manager.designation',
        ])->findOrFail($id);

        return view('users.view', compact('user'));
    }



    /** Public digital visiting card: /card/first-last (e.g. /card/jagruti-patil). */
    public function card($slug)
    {
        $user = $this->findCardUser($slug);

        if (! $user) {
            abort(404);
        }

        // People who have left: no contact details, only a short note.
        if (strcasecmp((string) $user->status, 'Exited') === 0) {
            return response()->view('users.card-inactive', compact('user'), 410);
        }

        $user->loadMissing(['designation', 'businessUnit', 'location']);

        return view('users.card', compact('user'));
    }

    /**
     * Find the person for a card link. Exact "first last" (or "first middle last") wins,
     * then the old loose match; active people come before people who have left.
     */
    private function findCardUser(string $slug): ?User
    {
        $slug = Str::slug($slug);
        if ($slug === '') {
            return null;
        }
        $words = explode('-', $slug);
        $activeFirst = fn ($q) => $q->orderByRaw("CASE WHEN status = 'Active' THEN 0 ELSE 1 END")->orderBy('id');

        $exact = $activeFirst(User::query()->where(function ($q) use ($slug) {
            $q->whereRaw("LOWER(REPLACE(TRIM(CONCAT_WS(' ', first_name, last_name)), ' ', '-')) = ?", [$slug])
              ->orWhereRaw("LOWER(REPLACE(TRIM(CONCAT_WS(' ', first_name, middle_name, last_name)), ' ', '-')) = ?", [$slug]);
        }))->first();
        if ($exact) {
            return $exact;
        }

        $query = User::query();
        foreach ($words as $part) {
            $query->whereRaw("LOWER(CONCAT_WS(' ', first_name, middle_name, last_name)) LIKE ?", ["%{$part}%"]);
        }

        return $activeFirst($query)->first();
    }

    public function downloadVcf($id)
    {
        $user = User::with(['designation', 'businessUnit'])->findOrFail($id);

        // No public contact file for people who have left.
        abort_if(strcasecmp((string) $user->status, 'Exited') === 0, 404);

        $esc = fn ($v) => str_replace(["\\", ',', ';', "\n"], ["\\\\", '\\,', '\\;', '\\n'], (string) $v);
        $first = trim((string) $user->first_name);
        $last = trim((string) $user->last_name);
        $phone = \App\Support\PeopleFormat::intlPhone($user->official_contact ?: $user->personal_contact);
        $email = $user->official_email ?: $user->email;

        $vcf = "BEGIN:VCARD\r\n";
        $vcf .= "VERSION:3.0\r\n";
        $vcf .= 'N:'.$esc($last).';'.$esc($first).";;;\r\n";
        $vcf .= 'FN:'.$esc(trim($first.' '.$last))."\r\n";
        if ($user->businessUnit) {
            $vcf .= 'ORG:'.$esc($user->businessUnit->name)."\r\n";
        }
        if ($user->designation) {
            $vcf .= 'TITLE:'.$esc($user->designation->name)."\r\n";
        }
        if ($phone) {
            $vcf .= "TEL;TYPE=CELL,WORK:+{$phone}\r\n";
        }
        if ($email) {
            $vcf .= 'EMAIL;TYPE=INTERNET,WORK:'.$esc($email)."\r\n";
        }
        if ($user->businessUnit && $user->businessUnit->domain) {
            $vcf .= 'URL:'.$esc($user->businessUnit->domain)."\r\n";
        }
        $vcf .= 'NOTE:'.$esc('Digital card: '.route('users.card', Str::slug(trim($first.' '.$last))))."\r\n";
        $vcf .= "END:VCARD\r\n";

        return response($vcf)
            ->header('Content-Type', 'text/vcard; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="'.Str::slug($first.'-'.$last).'.vcf"');
    }

    // ✅ Show the import page (upload form)
    // public function showprocess()
    // {
    //     return view('users.import');
    // }

    // // ✅ Handle the import upload
    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,csv'
    //     ]);

    //     $file = $request->file('file');

    //     // Sanitize headers first (optional, just for logging/debug)
    //     $cleanHeaders = ExcelSanitizer::sanitizeHeaders($file->getRealPath());
    //     logger('Sanitized Headers: ' . implode(', ', $cleanHeaders));

    //     $import = new ImportUser();
    //     Excel::import($import, $file);

    //     // Get skipped rows including duplicates
    //     $skipped = $import->failures(); 

    //     $messages = [];
    //     foreach ($skipped as $failure) {
    //         $messages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
    //     }

    //     // You can also log duplicates separately
    //     logger('Skipped duplicates during import');

    //     return redirect()->back()->with('success', 'Users imported successfully!')
    //                             ->with('skipped', $messages);
    // }

    // // ✅ Download import template
    // public function downloadTemplate()
    // {
    //     $headers = [
    //         'employee_code', 'entity', 'title', 'first_name', 'middle_name', 'last_name', 'gender', 'status',
    //         'official_contact', 'personal_contact', 'official_email', 'personal_email',
    //         'department', 'designation', 'role', 'reporting_manager', 'location_handled', 'work_location',
    //         'joining_date', 'confirm_date', 'leaving_date', 'exit_status', 'reason_for_leaving', 'fnf_status',
    //         'current_ctc', 'monthly_basic', 'monthly_hra', 'special_allowance', 'conveyance_allowance',
    //         'medical_reimbursement', 'professional_tax', 'pf_employer', 'pf_employee', 'net_deductions', 'net_salary',
    //         'pf_status', 'uan_number', 'bank_name', 'ifsc_code', 'bank_account_number',
    //         'dob', 'blood_group', 'communication_address', 'permanent_address', 'languages_known', 'education_qualification',
    //         'marital_status', 'marriage_date', 'spouse_name', 'parents_contact', 'emergency_contact_name',
    //         'emergency_contact_relationship', 'emergency_contact_number', 'pan_no', 'aadhar_no',
    //         'work_off', 'additional_comments'
    //     ];

    //     $filePath = storage_path('app/public/user_import_template.csv');
    //     $file = fopen($filePath, 'w');
    //     fputcsv($file, $headers);
    //     fclose($file);

    //     return response()->download($filePath, 'user_import_template.csv');
    // }
    /**
     * Org chart: who reports to whom, today or on any past date (from the manager history).
     * Built here as a tree; the page shows it as an expandable list that works on phones.
     */
    public function orgTree(Request $request)
    {
        $today = now()->toDateString();
        try {
            $date = $request->filled('date') ? \Carbon\Carbon::parse($request->date)->toDateString() : $today;
        } catch (\Throwable $e) {
            $date = $today;
        }
        if ($date > $today) {
            $date = $today;
        }
        $isToday = $date === $today;

        // People employed on that date.
        $people = User::with('designation:id,name')
            ->where(fn ($q) => $q->whereNull('joining_date')->orWhere('joining_date', '<=', $date))
            ->when($isToday,
                fn ($q) => $q->where('status', 'Active'),
                // Past date: had not left yet. People who left without a leaving date can't be placed.
                fn ($q) => $q->where(fn ($w) => $w->where('leaving_date', '>', $date)
                    ->orWhere(fn ($x) => $x->whereNull('leaving_date')->where('status', 'Active'))))
            ->get(['id', 'name', 'employee_code', 'designation_id', 'reporting_manager_id', 'status', 'photo']);

        // Manager on that date: from the history; today (or no history) falls back to the profile.
        $histManager = [];
        if (! $isToday) {
            $histManager = \App\Models\EmployeeReportingManagerHistory::where('effective_from', '<=', $date)
                ->where(fn ($q) => $q->whereNull('effective_to')->orWhere('effective_to', '>=', $date))
                ->orderBy('effective_from')->get(['user_id', 'reporting_manager_id'])
                ->pluck('reporting_manager_id', 'user_id')->all();
        }

        $ids = $people->pluck('id')->flip();
        $managerOf = [];
        $children = [];
        foreach ($people as $p) {
            $m = $histManager[$p->id] ?? $p->reporting_manager_id;
            $managerOf[$p->id] = $m;
            if ($m && isset($ids[$m]) && $m !== $p->id) {
                $children[$m][] = $p->id;
            }
        }

        $byId = $people->keyBy('id');
        $sortByName = fn ($list) => collect($list)->sortBy(fn ($id) => $byId[$id]->name)->values()->all();
        foreach ($children as $m => $list) {
            $children[$m] = $sortByName($list);
        }

        // Team size (everyone below), with a guard against loops in bad data.
        $total = [];
        $count = function ($id, $seen = []) use (&$count, &$total, $children) {
            if (isset($total[$id])) return $total[$id];
            if (isset($seen[$id])) return 0;
            $seen[$id] = true;
            $n = 0;
            foreach ($children[$id] ?? [] as $c) { $n += 1 + $count($c, $seen); }
            return $total[$id] = $n;
        };

        // Tops of the tree: no manager, or a manager who is not employed on that date.
        $roots = [];
        $orphans = [];
        foreach ($people as $p) {
            $m = $managerOf[$p->id];
            if (! $m) {
                $roots[] = $p->id;
            } elseif (! isset($ids[$m])) {
                $orphans[] = $p->id;
            }
        }
        // People in a reporting loop never reach a top: list them with the orphans.
        $reached = [];
        $walk = function ($id) use (&$walk, &$reached, $children) {
            if (isset($reached[$id])) return;
            $reached[$id] = true;
            foreach ($children[$id] ?? [] as $c) $walk($c);
        };
        foreach (array_merge($roots, $orphans) as $r) $walk($r);
        foreach ($people as $p) {
            if (! isset($reached[$p->id])) { $orphans[] = $p->id; $walk($p->id); }
        }
        foreach ($people as $p) $count($p->id);

        // Big teams first at the top level.
        usort($roots, fn ($a, $b) => ($total[$b] ?? 0) <=> ($total[$a] ?? 0) ?: strcmp($byId[$a]->name, $byId[$b]->name));
        $orphans = $sortByName(array_unique($orphans));
        $formerManagers = User::whereIn('id', collect($orphans)->map(fn ($id) => $managerOf[$id])->filter()->unique())
            ->get(['id', 'name', 'employee_code', 'status'])->keyBy('id');

        return view('users.org-tree', [
            'date' => $date,
            'isToday' => $isToday,
            'byId' => $byId,
            'children' => $children,
            'total' => $total,
            'roots' => $roots,
            'orphans' => $orphans,
            'managerOf' => $managerOf,
            'formerManagers' => $formerManagers,
            'headcount' => $people->count(),
        ]);
    }

    public function showImportForm()
    {
        return view('users.import'); // the blade above
    }
    public function importUser(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls|max:10240'
            ]);

            $file = $request->file('file');

            if (!$file->isValid()) {
                return redirect()->back()->with('error', 'Invalid file upload.');
            }

            $import = new ImportUser();

            \DB::beginTransaction();

            Excel::import($import, $file);

            \DB::commit();

            $errors = $import->getErrors();
            $successCount = $import->getSuccessCount();

            if (!empty($errors)) {
                return redirect()->back()->with([
                    'warning' => 'Import completed with some errors',
                    'import_errors' => $errors,
                    'success_count' => $successCount
                ]);
            }

            return redirect()->back()->with(
                'success',
                "Import successful. {$successCount} users processed."
            );

        } catch (\Throwable $e) {

            \DB::rollBack();

            \Log::error('User Import Failed', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with(
                'error',
                'Import failed: ' . $e->getMessage()
            );
        }
    }
}

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

        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $filters = array_filter($request->only([
                'entity',
                'work_location_id',
                'department_id',
                'designation_id',
                'role_id'
            ]));

            $users = $this->userRepository->getUsers($filters);

            return DataTables::of($users)
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
                    $fullName = preg_replace('/\s+/', ' ', trim($row->first_name .' '. $row->middle_name .' '. $row->last_name));
                    $slug = strtolower(str_replace(' ', '-', $fullName));
                    $actions = '<a class="dropdown-item" href="'.route('users.show', $row->id).'">
                                    <i class="bx bx-show me-1"></i> View
                                </a>';
                    $actions .= '<a class="dropdown-item" target="_blank" href="'.route('users.card', $slug).'">
                                    <i class="bx bx-show me-1"></i> Card
                                </a>';
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

                    if (auth()->user()->can('configuration-view')) {
                        $actions .= '<a class="dropdown-item" href="'.route('indent_configuration.index').'?user_id='.$row->id.'">
                                        <i class="bx bx-list-ul me-1"></i> Indent Configuration
                                    </a>';
                    }
                    // ✅ NEW: Add Salary / View Salary
                    if(auth()->user()->can('salary-view')) {
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
                ->rawColumns(['action'])
                ->make(true);
        }

        $locations = Location::select(['id', 'name'])->orderBy('name')->get();
        $departments = Department::select(['id', 'name'])->orderBy('name')->get();
        $designations = Designation::select(['id', 'name'])->orderBy('name')->get();
        $roles = Role::select(['id', 'name'])->orderBy('name')->get();
        $businessUnits = BusinessUnit::where('status', 1)->pluck('name', 'id');
        return view('users.index', compact('locations', 'departments', 'designations', 'roles','businessUnits'));
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
        //dd("1");
        // ✅ Validate core required fields
        // $validated = $request->validate([
        //     'employee_code' => 'required|unique:users,employee_code',
        //     'entity' => 'required|string',
        //     'first_name' => 'required|string|max:255',
        //     'last_name' => 'required|string|max:255',
        //     'role_id' => 'required|numeric',
        //     'official_email' => 'nullable|email|unique:users,official_email',
        //     'photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        // ]);
        //dd("2");
        // ✅ Handle photo upload
        $photoPath = $request->hasFile('photo') ? $request->file('photo')->store('user_photos', 'public') : null;

        // ✅ Calculate salary components server-side
        $ctc = floatval($request->current_ctc ?? 0);
        $monthly = $ctc / 12;
        $monthly_basic = $monthly * 0.5;
        $monthly_hra = $monthly_basic * 0.5;
        $special_allowance = $monthly * 0.1;
        $conveyance_allowance = $monthly * 0.1;
        $medical_reimbursement = $monthly * 0.05;
        $pfEmployer = 1800;
        $pfEmployee = 1800;
        $profTax = 200;
        $deductions = $pfEmployee + $pfEmployer + $profTax;
        $net_salary = $monthly - $deductions;

        // ✅ Create new user
        $user = new User();
        $user->employee_code = $request->employee_code;
        $user->entity = $request->entity;
        $user->title = $request->title;
        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->gender = $request->gender;
        $user->photo = $photoPath;
        $user->status = $request->status ?? 'Active';
        $user->name = trim($request->title . ' ' . $request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name);
        // 🔹 Add business unit
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
        $user->confirm_date = $request->confirm_date;
        $user->leaving_date = $request->leaving_date;
        $user->exit_status = $request->exit_status;
        $user->reason_for_leaving = $request->reason_for_leaving;
        $user->fnf_status = $request->fnf_status;

        // Salary & Compensation (server-side calculated)
        $user->current_ctc = $ctc;
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
        $user->pf_status = $request->pf_status == 'Active' ? 1 : 0; // map to int

        $user->uan_number = $request->uan_number;
        $user->bank_name = $request->bank_name;
        $user->ifsc_code = $request->ifsc_code;
        $user->bank_account_number = $request->bank_account_number;
        
        // Personal & Emergency
        $personal_fields = [
            'dob', 'blood_group', 'communication_address', 'permanent_address',
            'languages_known', 'education_qualification', 'marital_status', 'marriage_date',
            'spouse_name', 'parents_contact', 'emergency_contact_name', 'emergency_contact_relationship',
            'emergency_contact_number', 'pan_no', 'aadhar_no'
        ];
        foreach ($personal_fields as $field) {
            $user->$field = $request->$field;
        }

        // Assets & Misc
        $user->work_off = $request->work_off;
        $user->additional_comments = $request->additional_comments;

        // Default password
        $user->password = Hash::make('Welcome@123');
        //dd("3");
        $user->business_unit_id = $request->business_unit_id;

        $user->save();
        //dd("4");
        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $departments = Department::all();
        $designations = Designation::all();
        $roles = Role::all();
        $reportingUsers = User::all();
        $locations = Location::all();
        $businessUnits = BusinessUnit::where('status', 1)->pluck('name', 'id');
        $users = User::where('id', '!=', $id)->get(); // for reporting user dropdown

        return view('users.edit', compact(
            'user',
            'roles',
            'departments',
            'designations',
            'reportingUsers',
            'locations',
            'users',
            'businessUnits'
        ));
    }

    public function update(UserRequest $request, $id)
    {
        
       // print_r($request->all); exit;
        $user = User::findOrFail($id);
       
        $this->userRepository->updateUser($user, $request);

        // ✅ Handle photo upload separately
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if (!empty($user->photo) && file_exists(storage_path('app/public/'.$user->photo))) {
                unlink(storage_path('app/public/'.$user->photo));
            }

            // Store new photo in public disk
            $user->photo = $request->file('photo')->store('uploads/users', 'public');
            $user->save();
        }

        // ✅ Update role
        if ($request->filled('role_id')) {
            $user->syncRoles([$request->role_id]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }



    public function destroy($id)
    {
        User::where('id', $id)->delete();
        return redirect()->route('users.index')->with('success', 'User Deleted Successfully');
    }

    public function account()
    {
        return view('users.account');
    }

    public function updateProfile(UserProfileRequest $request)
    {
        $user = User::find(auth()->user()->id);
        $user->name = $request->name;
        $user->dob = $request->dob;
        $user->gender = $request->gender;
        $user->save();

        if ($request->has('photo')) {
            if ($user->photo != "") {
                unlink(storage_path('app/'.$user->photo));
            }
            $user->photo = uploadFile($request->file('photo'), config('uploadfilepath.USER_PROFILE_PHOTO'));
            $user->save();
        }

        return redirect()->route('account')->with('success', 'Profile Updated Successfully');
    }

    // public function importUser()
    // {
    //   //  echo "dd"; exit;
    //     Excel::import(new ImportUser, storage_path('app/employees.xlsx'));
    //     Excel::import(new UpdateUserReporting, storage_path('app/employees.xlsx'));
    // }
    public function importUser()
    {
        
        try {

            $import = new ImportUser();

            Excel::import($import, storage_path('app/employees.xlsx'));

            // Reporting manager import
            Excel::import(new UpdateUserReporting, storage_path('app/employees.xlsx'));

            // Partial success
            if (!empty($import->errors)) {
                return redirect()->back()->with([
                    'warning' => 'Import completed with errors',
                    'import_errors' => $import->errors,
                    'success_count' => $import->successCount
                ]);
            }

            // Full success
            // return redirect()->back()->with(
            //     'success',
            //     "Import successful. {$import->successCount} users imported."
            // );
            return response('Users imported successfully.');


        } catch (\Throwable $e) {

            \Log::error('User Import Failed', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with(
                'error',
                'Import failed: ' . $e->getMessage()
            );
        }
    }

    public function show($id)
    {
        $user = User::find($id);
        return view('users.view', compact('user'));
    }

    public function card($slug)
    {
        // Convert - to spaces
        $slug = str_replace('-', ' ', strtolower($slug));

        // Split slug into words
        $parts = explode(' ', $slug);

        // Build query
        $query = User::query();

        foreach ($parts as $part) {
            $query->whereRaw("LOWER(CONCAT_WS(' ', first_name, middle_name, last_name)) LIKE ?", ["%{$part}%"]);
        }

        $user = $query->firstOrFail();

        return view('users.card', compact('user'));
    }

    public function downloadVcf($id)
    {
        $user = User::findOrFail($id);

        // Build vCard
        $vcf = "BEGIN:VCARD\r\n";
        $vcf .= "VERSION:3.0\r\n";
        $vcf .= "FN:{$user->first_name} {$user->last_name}\r\n";

        // Optional: Add organization or title
        if ($user->businessUnit) {
            $vcf .= "ORG:{$user->businessUnit->name}\r\n";
        }
        if ($user->designation) {
            $vcf .= "TITLE:{$user->designation->name}\r\n";
        }

        // Phone numbers
        if ($user->phone) {
            $vcf .= "TEL;TYPE=CELL:{$user->phone}\r\n";
        }

        // Email
        if ($user->email) {
            $vcf .= "EMAIL;TYPE=INTERNET:{$user->email}\r\n";
        }

        // Optional: website
        if ($user->businessUnit && $user->businessUnit->domain) {
            $vcf .= "URL:{$user->businessUnit->domain}\r\n";
        }

        $vcf .= "END:VCARD\r\n";

        return response($vcf)
            ->header('Content-Type', 'text/vcard')
            ->header('Content-Disposition', 'attachment; filename="'.Str::slug($user->first_name.'-'.$user->last_name).'.vcf"');
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


}

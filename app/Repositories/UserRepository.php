<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function getUsers($filters = [])
    {
        // ✅ Remove manual join to roles (Spatie handles this via pivot)
        $query = User::with(['roles', 'location', 'department', 'designation'])
            ->select([
                'users.id',
                'users.employee_code',
                'users.name',
                'users.email',
                'users.entity as company',
                'users.work_location_id',
                'users.department_id',
                'users.designation_id',
                'users.role_id', // keep legacy fallback if needed
            ])
            ->leftJoin('locations', 'users.work_location_id', '=', 'locations.id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->leftJoin('designations', 'users.designation_id', '=', 'designations.id');

        // ✅ Filters
        if (!empty($filters['entity'])) {
            $query->where('users.entity', $filters['entity']);
        }

        if (!empty($filters['work_location_id'])) {
            $query->where('users.work_location_id', $filters['work_location_id']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('users.department_id', $filters['department_id']);
        }

        if (!empty($filters['designation_id'])) {
            $query->where('users.designation_id', $filters['designation_id']);
        }

        if (!empty($filters['role_id'])) {
            $roleId = $filters['role_id'];
            $query->whereHas('roles', function ($q) use ($roleId) {
                $q->where('roles.id', $roleId);
            });
        }

        return $query;
    }


    public function updateUser($user, $request)
    {
        // ✅ Update core details
        $user->employee_code = $request->employee_code;
        $user->entity = $request->entity;
        $user->title = $request->title;
        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;

        // ✅ Auto-generate full name
        $user->name = trim($request->title . ' ' . $request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name);

        $user->gender = $request->gender;
        $user->status = $request->status ?? 'Active';

        // ✅ Contact Information
        $user->official_contact = $request->official_contact;
        $user->personal_contact = $request->personal_contact;
        $user->official_email = $request->official_email;
        $user->personal_email = $request->personal_email;
        $user->email = $request->official_email; // Login email

        // ✅ Employment Details
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
        $user->business_unit_id = $request->business_unit_id;
        
        // ✅ Salary Calculation
        $ctc = floatval($request->current_ctc ?? $user->current_ctc ?? 0);
        $monthly = $ctc / 12;
        $user->current_ctc = $ctc;
        $user->monthly_basic = $monthly * 0.5;
        $user->monthly_hra = $user->monthly_basic * 0.5;
        $user->special_allowance = $monthly * 0.1;
        $user->conveyance_allowance = $monthly * 0.1;
        $user->medical_reimbursement = $monthly * 0.05;
        $user->pf_employer = 1800;
        $user->pf_employee = 1800;
        $user->professional_tax = 200;
        $user->net_deductions = $user->pf_employer + $user->pf_employee + $user->professional_tax;
        $user->net_salary = $monthly - $user->net_deductions;

        // ✅ Statutory & Banking
        // ✅ Convert pf_status (Active/Yes/1 → 1, others → 0)
        if ($request->has('pf_status')) {
            $status = strtolower(trim($request->pf_status));
            $user->pf_status = in_array($status, ['1', 'yes', 'true', 'active']) ? 1 : 0;
        }
        $user->uan_number = $request->uan_number;
        $user->bank_name = $request->bank_name;
        $user->ifsc_code = $request->ifsc_code;
        $user->bank_account_number = $request->bank_account_number;

        // ✅ Personal & Emergency Info
        $personal_fields = [
            'dob', 'blood_group', 'communication_address', 'permanent_address',
            'languages_known', 'education_qualification', 'marital_status', 'marriage_date',
            'spouse_name', 'parents_contact', 'emergency_contact_name', 'emergency_contact_relationship',
            'emergency_contact_number', 'pan_no', 'aadhar_no'
        ];

        foreach ($personal_fields as $field) {
            $user->$field = $request->$field;
        }

        // ✅ Company Assets & Miscellaneous
        $user->work_off = $request->work_off;
        $user->additional_comments = $request->additional_comments;

        // ✅ Save all updates
        $user->save();

        return $user;
    }


}

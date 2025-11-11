<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Location;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ImportUser implements ToCollection, WithHeadingRow
{
    // Map to store employee_code => user_id
    private $userMap = [];

    // Store manager mapping to update after import
    private $managerMap = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip empty employee_code
            if (empty($row['employee_code'])) continue;

            // Department / Designation / Location / Role
            $department = Department::firstOrCreate(['name' => $row['department'] ?? '']);
            $designation = Designation::firstOrCreate(['name' => $row['designation'] ?? '']);
            $location = Location::firstOrCreate(
                ['city' => $row['work_location'] ?? ''],
                ['created_by' => auth()->id() ?? 1]
            );
            $role = Role::firstOrCreate(['name' => $row['role'] ?? 'Employee']);

            // Create / update user
            $user = User::updateOrCreate(
                ['employee_code' => $row['employee_code']],
                [
                    'entity' => $row['entity'],
                    'title' => $row['title'],
                    'first_name' => $row['first_name'],
                    'middle_name' => $row['middle_name'] ?? null,
                    'last_name' => $row['last_name'] ?? null,
                    'name' => trim(($row['title'] ?? '') . ' ' . ($row['first_name'] ?? '') . ' ' . ($row['middle_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
                    'gender' => $row['gender'] ?? null,
                    'status' => $row['status'] ?? 'Active',
                    'official_contact' => $row['official_contact'] ?? null,
                    'personal_contact' => $row['personal_contact'] ?? null,
                    'official_email' => $row['official_email'] ?? null,
                    'personal_email' => $row['personal_email'] ?? null,
                    'email' => $row['official_email'] ?? null,
                    'department_id' => $department->id,
                    'designation_id' => $designation->id,
                    'role_id' => $role->id,
                    'location_handled' => $row['location_handled'] ?? null,
                    'work_location_id' => $location->id,

                    // ✅ Safe date parsing
                    'joining_date' => $this->parseExcelDate($row['joining_date'] ?? null),
                    'confirm_date' => $this->parseExcelDate($row['confirm_date'] ?? null),
                    'leaving_date' => $this->parseExcelDate($row['leaving_date'] ?? null),
                    'pf_joining_date' => $this->parseExcelDate($row['joining_date'] ?? null)
                        ? Carbon::parse($this->parseExcelDate($row['joining_date']))->addMonths(6)->format('Y-m-d')
                        : null,

                    'exit_status' => $row['exit_status'] ?? null,
                    'reason_for_leaving' => $row['reason_for_leaving'] ?? null,
                    'fnf_status' => $row['fnf_status'] ?? null,
                    'current_ctc' => $row['current_ctc'] ?? null,
                    'monthly_basic' => $row['monthly_basic'] ?? null,
                    'monthly_hra' => $row['monthly_hra'] ?? null,
                    'special_allowance' => $row['special_allowance'] ?? null,
                    'conveyance_allowance' => $row['conveyance_allowance'] ?? null,
                    'medical_reimbursement' => $row['medical_reimbursement'] ?? null,
                    'professional_tax' => $row['professional_tax'] ?? null,
                    'pf_employer' => $row['pf_employer'] ?? null,
                    'pf_employee' => $row['pf_employee'] ?? null,
                    'net_deductions' => $row['net_deductions'] ?? null,
                    'net_salary' => $row['net_salary'] ?? null,
                    'pf_status' => in_array(strtolower(trim($row['pf_status'] ?? '')), ['1', 'yes', 'true', 'active']) ? 1 : 0,

                    'uan_number' => $row['uan_number'] ?? null,
                    'bank_name' => $row['bank_name'] ?? null,
                    'ifsc_code' => $row['ifsc_code'] ?? null,
                    'bank_account_number' => $row['bank_account_number'] ?? null,

                    // ✅ DOB and age with safe parsing
                    'dob' => $this->parseExcelDate($row['dob'] ?? null),
                    'age' => !empty($row['dob']) ? Carbon::parse($this->parseExcelDate($row['dob']))->age : null,
                    'birthday_month' => !empty($row['dob']) ? Carbon::parse($this->parseExcelDate($row['dob']))->format('F') : null,

                    'blood_group' => $row['blood_group'] ?? null,
                    'communication_address' => $row['communication_address'] ?? null,
                    'permanent_address' => $row['permanent_address'] ?? null,
                    'languages_known' => $row['languages_known'] ?? null,
                    'education_qualification' => $row['education_qualification'] ?? null,
                    'marital_status' => $row['marital_status'] ?? null,

                    // ✅ Marriage date safe parsing
                    'marriage_date' => $this->parseExcelDate($row['marriage_date'] ?? null),

                    'spouse_name' => $row['spouse_name'] ?? null,
                    'parents_contact' => $row['parents_contact'] ?? null,
                    'emergency_contact_name' => $row['emergency_contact_name'] ?? null,
                    'emergency_contact_relationship' => $row['emergency_contact_relationship'] ?? null,
                    'emergency_contact_number' => $row['emergency_contact_number'] ?? null,
                    'pan_no' => $row['pan_no'] ?? null,
                    'aadhar_no' => $row['aadhar_no'] ?? null,
                    'work_off' => $row['work_off'] ?? null,
                    'additional_comments' => $row['additional_comments'] ?? null,
                    'password' => Hash::make('Welcome@123'),
                ]
            );

            // Assign Spatie role
            $user->syncRoles($role->name);

            // Store mapping
            $this->userMap[$row['employee_code']] = $user->id;

            // Store reporting manager code for later update
            if (!empty($row['reporting_manager'])) {
                $this->managerMap[$row['employee_code']] = $row['reporting_manager'];
            }
        }

        // Update reporting_manager_id after all users are created
        foreach ($this->managerMap as $empCode => $managerCode) {
            if (isset($this->userMap[$managerCode])) {
                User::where('employee_code', $empCode)
                    ->update(['reporting_manager_id' => $this->userMap[$managerCode]]);
            }
        }
    }

    /**
     * Safely parse Excel or text date into Y-m-d format
     */
    private function parseExcelDate($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        // Numeric Excel date (e.g. 45293)
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // If it starts with "=" (formula), skip
        if (preg_match('/^=/', $value)) {
            return null;
        }

        // Text dates like "02-Nov-24", "2 November 2024", "2024-11-02"
        if (strtotime($value)) {
            return date('Y-m-d', strtotime($value));
        }

        return null;
    }
}

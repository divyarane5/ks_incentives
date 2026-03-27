<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Location;
use App\Models\BusinessUnit;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class ImportUser implements ToCollection
{
    private $successCount = 0;
    private $errors = [];
    private $userMap = [];
    private $managerMap = [];

    public function collection(Collection $rows)
    {
        $header = $rows->first()->map(fn($item) => strtolower(trim($item)));
        $rows = $rows->slice(1);

        foreach ($rows as $index => $row) {
            try {
                $row = $header->combine($row);
                if (empty($row['employee_code'])) continue;

                // ✅ Masters
                $department = Department::firstOrCreate(
                    ['name' => $this->nullIfNA($row['department'] ?? null)],
                    ['created_by' => auth()->id() ?? 1]
                );

                $designation = Designation::firstOrCreate(
                    ['name' => $this->nullIfNA($row['designation'] ?? null)],
                    ['created_by' => auth()->id() ?? 1]
                );

                $location = Location::firstOrCreate(
                    ['name' => $this->nullIfNA($row['work_location'] ?? null)],
                    ['created_by' => auth()->id() ?? 1]
                );

                $businessUnit = BusinessUnit::firstOrCreate(
                    ['name' => $this->nullIfNA($row['business_unit'] ?? null)],
                    ['created_by' => auth()->id() ?? 1]
                );

                // ✅ Emergency split
                $emergency = $this->splitEmergency($row['emergency_contact_name'] ?? null);

                // ✅ Dates
                $joiningDate = $this->parseDate($row['joining_date'] ?? null);
                $confirmDate = $this->parseDate($row['confirm_date'] ?? null);

                // ✅ Probation days
                $probationDays = ($joiningDate && $confirmDate)
                    ? (strtotime($confirmDate) - strtotime($joiningDate)) / (60 * 60 * 24)
                    : null;
                $email = $this->nullIfNA($row['official_email'] ?? null);

                if ($email) {
                    $existingUser = User::where('email', $email)->first();

                    if ($existingUser && $existingUser->employee_code != $row['employee_code']) {
                        $this->errors[] = "Duplicate email found: " . $email;
                        continue; // skip row
                    }
                }
                // ✅ Create / Update User
                $user = User::updateOrCreate(
                    [
                        'employee_code' => $row['employee_code'], // ✅ unique key
                    ],
                    [
                        'email' => $email, // already cleaned above
                        'official_email' => $email,
                        'employee_code' => $row['employee_code'],

                        'entity' => 'Keystone Real Estate Advisory Pvt. Ltd.',
                        'name' => $this->nullIfNA($row['name'] ?? null),
                        'title' => $this->nullIfNA($row['title'] ?? null),
                        'first_name' => $this->nullIfNA($row['first_name'] ?? null),
                        'middle_name' => $this->nullIfNA($row['middle_name'] ?? null),
                        'last_name' => $this->nullIfNA($row['last_name'] ?? null),
                        'gender' => $this->nullIfNA($row['gender'] ?? null),
                        'status' => $this->nullIfNA($row['status'] ?? null),
                        'official_contact' => $this->nullIfNA($row['official_contact'] ?? null),
                        'personal_contact' => $this->nullIfNA($row['personal_contact'] ?? null),
                        'email' => $this->nullIfNA($row['official_email'] ?? null),
                        'official_email' => $this->nullIfNA($row['official_email'] ?? null),
                        'personal_email' => $this->nullIfNA($row['personal_email'] ?? null),
                        'department_id' => $department->id ?? null,
                        'designation_id' => $designation->id ?? null,
                        'business_unit_id' => $businessUnit->id ?? null,
                        'work_location_id' => $location->id ?? null,
                        'location_handled' => $this->nullIfNA($row['location_handled'] ?? null),
                        'joining_date' => $joiningDate,
                        'confirm_date' => $confirmDate,
                        'leaving_date' => $this->parseDate($row['leaving_date'] ?? null),
                        'probation_period_days' => $probationDays,
                        'employment_status' => strtolower($row['employment_status'] ?? 'probation'),
                        'exit_status' => $this->nullIfNA($row['exit_status'] ?? null),
                        'reason_for_leaving' => $this->nullIfNA($row['reason_for_leaving'] ?? null),
                        'fnf_status' => $this->nullIfNA($row['fnf_status'] ?? null),
                        'current_ctc' => $row['current_ctc'] ?? null,
                        'annual_ctc' => isset($row['current_ctc']) ? ($row['current_ctc'] * 12) : null,
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
                        'pf_status' => $this->pfStatus($row['pf_status'] ?? null),
                        'pf_joining_date' => $this->parseDate($row['pf_joining_date'] ?? null),
                        'uan_number' => $this->nullIfNA($row['uan_number'] ?? null),
                        'bank_name' => $this->nullIfNA($row['bank_name'] ?? null),
                        'ifsc_code' => $this->nullIfNA($row['ifsc_code'] ?? null),
                        'bank_account_number' => $this->nullIfNA($row['bank_account_number'] ?? null),
                        'bank_account_name' => $this->nullIfNA($row['bank_account_name'] ?? null),
                        'bank_branch_name' => $this->nullIfNA($row['bank_branch_name'] ?? null),
                        'bank_account_type' => $this->nullIfNA($row['bank_account_type'] ?? null),
                        'dob' => $this->parseDate($row['dob'] ?? null),
                        'age' => $row['age'] ?? null,
                        'birthday_month' => $this->nullIfNA($row['birthday_month'] ?? null),
                        'blood_group' => $this->nullIfNA($row['blood_group'] ?? null),
                        'communication_address' => $this->nullIfNA($row['communication_address'] ?? null),
                        'permanent_address' => $this->nullIfNA($row['permanent_address'] ?? null),
                        'languages_known' => $this->nullIfNA($row['languages_known'] ?? null),
                        'education_qualification' => $this->nullIfNA($row['education_qualification'] ?? null),
                        'marital_status' => $this->nullIfNA($row['marital_status'] ?? null),
                        'marriage_date' => $this->parseDate($row['marriage_date'] ?? null),
                        'spouse_name' => $this->nullIfNA($row['spouse_name'] ?? null),
                        'parents_contact' => $this->nullIfNA($row['parents_contact'] ?? null),
                        'emergency_contact_name' => $emergency['name'],
                        'emergency_contact_relationship' => $emergency['relation'],
                        'emergency_contact_number' => $emergency['number'],
                        // ✅ Letter flags
                        'offer_letter_sent' => $this->yesNo($row['offer_letter_sent'] ?? null),
                        'offer_letter_acknowledged' => 1,
                        'joining_letter_sent' => $this->yesNo($row['joining_letter_sent'] ?? null),
                        'joining_letter_acknowledged' => $this->yesNo($row['joining_letter_acknowledged'] ?? null),
                        'password' => Hash::make('Welcome@123'),
                    ]
                );

                // ✅ Role
                if (!empty($row['role'])) {
                    $role = Role::firstOrCreate(['name' => trim($row['role'])]);
                    $user->syncRoles([$role]);
                    $user->role_id = $role->id;
                    $user->save();
                }

                // ✅ Manager mapping
                if (!empty($row['reporting_manager'])) {
                    $this->managerMap[] = [
                        'user_id' => $user->id,
                        'manager_name' => $this->normalizeName($row['reporting_manager'])
                    ];
                }

                // ✅ Store mappings
                $this->userMap[$this->normalizeName($user->name)] = $user->id;
                $this->userMap[strtolower(trim($user->employee_code))] = $user->id;

                $this->successCount++;

            } catch (\Throwable $e) {
                $this->errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        // ✅ Assign managers after all users are created
        $this->assignManagers();
    }

    private function assignManagers()
    {
        for ($i = 0; $i < 3; $i++) {
            foreach ($this->managerMap as $key => $map) {

                $managerName = strtolower(trim($map['manager_name']));
                $managerId = $this->userMap[$managerName] ?? null;

                if ($managerId) {
                    User::where('id', $map['user_id'])
                        ->update(['reporting_manager_id' => $managerId]);

                    unset($this->managerMap[$key]);
                }
            }
        }
    }

    private function parseDate($value)
    {
        if (!$value || strtolower($value) === 'na') return null;
        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }
            return date('Y-m-d', strtotime($value));
        } catch (\Exception $e) {
            return null;
        }
    }

    private function yesNo($value)
    {
        if (!$value) return 0;

        // remove spaces, line breaks, tabs
        $value = strtolower(trim(preg_replace('/\s+/', ' ', $value)));

        if (in_array($value, ['yes','1','shared','done'])) return 1;

        if (in_array($value, ['no','0','not recieved','not received','na','n/a','not applicable'])) return 0;

        return 0;
    }
    private function splitEmergency($text)
    {
        $parts = explode('-', $text);
        return [
            'relation' => $parts[0] ?? null,
            'name' => $parts[1] ?? null,
            'number' => $parts[2] ?? null,
        ];
    }

    private function nullIfNA($value)
    {
        if ($value === null) return null;
        $value = trim($value);
        if ($value === '' || strtolower($value) === 'na' || strtolower($value) === 'n/a') return null;
        return $value;
    }

    private function pfStatus($value)
    {
        if (!$value) return 0;
        $map = ['pf pending'=>0,'pf company'=>1,'yes'=>1,'done'=>1];
        $value = strtolower(trim($value));
        return $map[$value] ?? 0;
    }

    public function getSuccessCount() { return $this->successCount; }
    public function getErrors() { return $this->errors; }

    private function normalizeName($name)
    {
        $name = strtolower(trim(preg_replace('/\s+/', ' ', $name)));

        $parts = explode(' ', $name);

        if (count($parts) >= 2) {
            return $parts[0] . ' ' . end($parts); // first + last
        }

        return $name;
    }
}
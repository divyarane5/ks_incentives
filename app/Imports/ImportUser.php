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
        foreach ($rows as $index => $row) {
            try {

                if (empty($row['employee_code'])) {
                    continue;
                }

                // ✅ Department
                $department = Department::firstOrCreate([
                    'name' => $row['department']
                ]);

                // ✅ Designation
                $designation = Designation::firstOrCreate([
                    'name' => $row['designation']
                ]);

                // ✅ Location
                $location = Location::firstOrCreate([
                    'name' => $row['work_location']
                ]);

                // ✅ Business Unit
                $businessUnit = BusinessUnit::firstOrCreate([
                    'name' => $row['business_unit']
                ]);

                // ✅ Create / Update User
                $user = User::updateOrCreate(
                    ['employee_code' => $row['employee_code']],
                    [
                        'entity' => $row['entity'],
                        'title' => $row['title'],
                        'first_name' => $row['first_name'],
                        'middle_name' => $row['middle_name'] ?? null,
                        'last_name' => $row['last_name'] ?? null,
                        'gender' => $row['gender'],
                        'status' => $row['status'],

                        'official_email' => $row['official_email'],
                        'personal_email' => $row['personal_email'],
                        'official_contact' => $row['official_contact'],
                        'personal_contact' => $row['personal_contact'],

                        'department_id' => $department->id,
                        'designation_id' => $designation->id,
                        'location_id' => $location->id,
                        'business_unit_id' => $businessUnit->id,

                        'joining_date' => $this->parseExcelDate($row['joining_date']),
                        'confirm_date' => $this->parseExcelDate($row['confirm_date']),
                        'leaving_date' => $this->parseExcelDate($row['leaving_date']),
                        'dob' => $this->parseExcelDate($row['dob']),

                        'exit_status' => $row['exit_status'],
                        'reason_for_leaving' => $row['reason_for_leaving'],
                        'fnf_status' => $row['fnf_status'],

                        'password' => Hash::make('123456'), // default password
                    ]
                );

                // ✅ Assign Role (Spatie)
                if (!empty($row['role'])) {
                    $role = Role::firstOrCreate(['name' => $row['role']]);
                    $user->syncRoles([$role]);
                }

                // ✅ Store for manager mapping (we’ll update later)
                if (!empty($row['reporting_manager'])) {
                    $this->managerMap[] = [
                        'user_id' => $user->id,
                        'manager_name' => $row['reporting_manager']
                    ];
                }

                // ✅ Store mapping for lookup
                $this->userMap[$row['first_name'] . ' ' . $row['last_name']] = $user->id;

                $this->successCount++;

            } catch (\Throwable $e) {

                $this->errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();

                \Log::error('Import row failed', [
                    'row' => $row,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // ✅ SECOND PASS → Update Reporting Manager
        $this->assignManagers();
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

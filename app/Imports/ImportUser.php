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

                // Force expected keys (you already did this correctly)
                $row = collect([
                    'exit_status' => null,
                    'reason_for_leaving' => null,
                    'fnf_status' => null,
                ])->merge($row);

                if (empty($row['employee_code'])) {
                    continue;
                }

                User::updateOrCreate(
                    ['employee_code' => $row['employee_code']],
                    [
                        'entity' => $row['entity'],
                        'title' => $row['title'],
                        'first_name' => $row['first_name'],
                        'middle_name' => $row['middle_name'] ?? null,
                        'last_name' => $row['last_name'] ?? null,

                        'exit_status' => $row['exit_status'],
                        'reason_for_leaving' => $row['reason_for_leaving'],
                        'fnf_status' => $row['fnf_status'],
                    ]
                );

                // ✅ COUNT SUCCESS
                $this->successCount++;

            } catch (\Throwable $e) {

                // ✅ COLLECT ERRORS (DON'T STOP IMPORT)
                $this->errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();

                \Log::error('Import row failed', [
                    'row' => $row,
                    'error' => $e->getMessage()
                ]);
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

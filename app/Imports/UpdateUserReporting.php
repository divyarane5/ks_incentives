<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class UpdateUserReporting implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if ($row[0] != "Employee ID") {
            $employeeCode = $row[0];
            $reportingCode = $row[11];
            $reportingToUser = User::where('employee_code', $reportingCode)->first();
            if (!empty($reportingToUser)) {
               User::where('employee_code', $employeeCode)->update(['reporting_user_id' => $reportingToUser->id]);
            }
        }
    }
}

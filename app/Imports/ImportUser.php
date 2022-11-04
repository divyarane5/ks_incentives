<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Location;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Hash;

class ImportUser implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $defaultRole = 2;
        if ($row[0] != "Employee ID") {
            $email = $row[3];
            $departmentName = $row[5];
            $designationName = $row[4];
            $locationName = $row[8];
            //check is user exist
            $userCount = User::where('employee_code', $row[0])->count();
            if ($userCount == 0) {
                //check for department
                $department = Department::where('name', $departmentName)->first();
                if (empty($department)) {
                    $department = new Department([
                        'name' => $departmentName
                    ]);
                }
                $departmentId = $department->id;

                //check for designation
                $designation = Designation::where('name', $designationName)->first();
                if (empty($designation)) {
                    $designation = new Designation([
                        'name' => $designationName
                    ]);
                }
                $designationId = $designation->id;

                //check for location
                $location = Location::where('name', $locationName)->first();
                if (empty($location)) {
                    $location = new Location([
                        'name' => $locationName
                    ]);
                }
                $locationId = $location->id;

                $userDetails = [
                                'employee_code' => $row[0],
                                'name' => $row[1].' '.$row[2],
                                'email' => $row[3],
                                'password' => Hash::make(ucfirst($row[1])."@123"),
                                'department_id' => $departmentId,
                                'designation_id' => $designationId,
                                'joining_date' => Date::excelToDateTimeObject(intval($row[6]))->format('Y-m-d'),
                                'dob' => Date::excelToDateTimeObject(intval($row[7]))->format('Y-m-d'),
                                'location_id' => $locationId,
                                'gender' => $row[9],
                                'entity' => $row[10],
                            ];
                $user =  User::create($userDetails);
                $user->assignRole([$defaultRole]);
            }

        }

    }
}

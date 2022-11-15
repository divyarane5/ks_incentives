<?php
namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Hash;

class UserRepository implements UserRepositoryInterface
{
    public function getUsers()
    {
        $user = User::select([
                'users.id',
                'employee_code',
                'users.name',
                'email',
                'designations.name as designation',
                'departments.name as department',
                'locations.name as location',
                'entity as company'
            ])
            ->leftJoin('designations', 'users.designation_id', '=', 'designations.id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->leftJoin('locations', 'users.location_id', '=', 'locations.id');
        return $user;
    }

    public function updateUser($user, $request)
    {

        $user->name = $request->input('name');
        $user->employee_code = $request->input('employee_code');
        $user->email = $request->input('email');
        if ($request->has('password') && !empty($request->input('password'))) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->entity = $request->input('entity');
        $user->location_id = $request->input('location_id');
        $user->department_id = $request->input('department_id');
        $user->designation_id = $request->input('designation_id');
        $user->dob = date("Y-m-d", strtotime($request->input('dob')));
        $user->joining_date = date("Y-m-d", strtotime($request->input('joining_date')));
        $user->gender = $request->input('gender');
        $user->reporting_user_id = $request->input('reporting_user_id');
        $user->save();

        //update role
        $user->assignRole([$request->input('role_id')]);
        return $user;
    }
}

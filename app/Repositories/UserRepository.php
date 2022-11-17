<?php
namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Hash;

class UserRepository implements UserRepositoryInterface
{
    public function getUsers($userRequest = [])
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

        if (isset($userRequest['entity']) && $userRequest['entity'] != "") {
            $user = $user->where('entity', $userRequest['entity']);
        }
        if (isset($userRequest['location_id']) && $userRequest['location_id'] != "") {
            $user = $user->where('location_id', $userRequest['location_id']);
        }
        if (isset($userRequest['department_id']) && $userRequest['department_id'] != "") {
            $user = $user->where('department_id', $userRequest['department_id']);
        }
        if (isset($userRequest['designation_id']) && $userRequest['designation_id'] != "") {
            $user = $user->where('designation_id', $userRequest['designation_id']);
        }
        if (isset($userRequest['role_id']) && $userRequest['role_id'] != "") {
            $roleId = $userRequest['role_id'];
            $user = $user->whereHas(
                    'roles', function($q) use($roleId) {
                        $q->where('id', $roleId);
                    }
                );
        }
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
        $user->syncRoles([$request->input('role_id')]);
        return $user;
    }
}

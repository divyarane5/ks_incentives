<?php
namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

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
            ->leftJoin('locations', 'users.location_id', '=', 'locations.id')
            ->orderBy('id','desc');
        return $user;
    }
}

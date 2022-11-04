<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'email' => 'admin@homebazaar.com',
            'password' => bcrypt('123456'),
            'employee_code' => 'VSH_OP_001',
            'entity' => 'Homebazaar',
            'name' => 'Homebazaar Admin',
            'created_by' => 1
        ]);

        $role = Role::create(['name' => 'Superadmin']);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);

        $userRole = Role::create(['name' => 'User']);

        $userRole->syncPermissions(['indent-view-own', 'indent-create', 'indent-edit']);
    }
}

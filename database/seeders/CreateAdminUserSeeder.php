<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BusinessUnit;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        // Fetch the business unit ID for Keystone Real Estate Advisory
        $businessUnit = BusinessUnit::where('name', 'Keystone Real Estate Advisory')->first();


        // ✅ 1. Ensure Superadmin role exists
        $role = Role::firstOrCreate(['name' => 'Superadmin'], ['guard_name' => 'web']);
        

        // ✅ 2. Get all permissions (including mandate project ones)
        $permissions = Permission::pluck('id', 'id')->all();
        $role->syncPermissions($permissions);

        // ✅ 3. Create admin user if not already existing
        $user = User::firstOrCreate(
            ['email' => 'admin@keystone.com'],
            [
                'password' => bcrypt('123456'),
                'employee_code' => 'KRE008',
                'entity' => 'Keystone Real Estate Advisory Pvt. Ltd.',
                'name' => 'Keystone Admin',
                'created_by' => 1,
                'business_unit_id' => $businessUnit ? $businessUnit->id : null, // Assign business unit
            ]
        );

        // ✅ 4. Assign Superadmin role
        if (!$user->hasRole('Superadmin')) {
            $user->assignRole($role);
        }

        // ✅ 5. (Optional) Ensure base User role exists
        $userRole = Role::firstOrCreate(['name' => 'User'], ['guard_name' => 'web']);
        $userRole->syncPermissions(['indent-view-own', 'indent-create', 'indent-edit']);
    }
}
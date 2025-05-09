<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionTableSeeder::class,
            CreateAdminUserSeeder::class,
            PaymentMethodSeeder::class,
            BusinessUnitSeeder::class,
            CreateTemplateSeeder::class,
            JoiningFormPermissionsSeeder::class
        ]);
    }
}

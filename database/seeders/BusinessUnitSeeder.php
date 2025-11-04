<?php

namespace Database\Seeders;

use App\Models\BusinessUnit;
use Illuminate\Database\Seeder;

class BusinessUnitSeeder extends Seeder
{
    public function run()
    {
        BusinessUnit::create([
            'name' => 'Keystone Real Estate Advisory',
            'code' => 'KREA',
            'domain' => 'krea.portal.com',
            'theme_color' => '#d97706',
            'secondary_color' => '#fef3c7',
            'status' => 1,
        ]);

        BusinessUnit::create([
            'name' => 'Designo Palette',
            'code' => 'DP',
            'domain' => 'dp.portal.com',
            'theme_color' => '#0d9488',
            'secondary_color' => '#ccfbf1',
            'status' => 1,
        ]);

        BusinessUnit::create([
            'name' => 'Keystone Finserv',
            'code' => 'KF',
            'domain' => 'kf.portal.com',
            'theme_color' => '#1e40af',
            'secondary_color' => '#c7d2fe',
            'status' => 1,
        ]);

        
    }
}

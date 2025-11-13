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
            'theme_color' => '#011e48',
            'secondary_color' => '#c3b246',
            'status' => 1,
        ]);

        BusinessUnit::create([
            'name' => 'Designo Palette',
            'code' => 'DP',
            'domain' => 'dp.portal.com',
            'theme_color' => '#06431e',
            'secondary_color' => '#dda05f',
            'status' => 1,
        ]);

        BusinessUnit::create([
            'name' => 'Keystone Finserv',
            'code' => 'KF',
            'domain' => 'kf.portal.com',
            'theme_color' => '#06431e',
            'secondary_color' => '#e8b71f',
            'status' => 1,
        ]);
        
        BusinessUnit::create([
            'name' => 'Alterra India',
            'code' => 'AI',
            'domain' => 'ai.portal.com',
            'theme_color' => '#2e6aa6',
            'secondary_color' => '#4eb3fb',
            'status' => 1,
        ]);
        
    }
}

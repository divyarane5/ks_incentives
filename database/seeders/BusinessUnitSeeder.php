<?php

namespace Database\Seeders;

use App\Models\BusinessUnit;
use Illuminate\Database\Seeder;

class BusinessUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BusinessUnit::create(['name' => 'Designo Palette']);
        BusinessUnit::create(['name' => 'Keystone Finserv']);
        BusinessUnit::create(['name' => 'Keystone Real Estate Advisory Pvt Ltd']);
    }
}

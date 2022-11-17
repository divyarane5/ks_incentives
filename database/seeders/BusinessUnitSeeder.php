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
        BusinessUnit::create(['name' => 'Homebazaar-CP']);
        BusinessUnit::create(['name' => 'Homebazaar-Home Loans']);
        BusinessUnit::create(['name' => 'Homebazaar-Website']);
    }
}

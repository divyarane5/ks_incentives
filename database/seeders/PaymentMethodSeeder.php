<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentMethod::create(['name' => 'Credit Card']);
        PaymentMethod::create(['name' => 'Accounts - monthly']);
        PaymentMethod::create(['name' => 'Accounts - immediate']);
        PaymentMethod::create(['name' => 'Accounts - weekly']);
    }
}

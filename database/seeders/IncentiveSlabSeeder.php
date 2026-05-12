<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IncentiveSlabSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $slabs = [

            ['from'=>0,  'to'=>4,  'percent'=>0],
            ['from'=>4,  'to'=>10, 'percent'=>5],
            ['from'=>10, 'to'=>15, 'percent'=>7.5],
            ['from'=>15, 'to'=>20, 'percent'=>10],
            ['from'=>20, 'to'=>25, 'percent'=>12.5],
            ['from'=>25, 'to'=>30, 'percent'=>15],
            ['from'=>30, 'to'=>35, 'percent'=>17.5],
            ['from'=>35, 'to'=>999999, 'percent'=>20],

        ];

        foreach($slabs as $slab){

            IncentiveSlab::create([

                'financial_year' => '2025-26',

                'role' => 'FOS',

                'from_times' => $slab['from'],

                'to_times' => $slab['to'],

                'incentive_percent' => $slab['percent'],

                'justification_multiplier' => 4,
            ]);
        }
    }
}

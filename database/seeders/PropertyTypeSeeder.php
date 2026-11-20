<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Residential' => ['Flat', 'Villa', 'Row Villa', 'Villament', 'Plot', 'Independent House', 'Floor', 'Kothi'],
            'Commercial' => [
                'Office Space', 'Co Working Office Space', 'Shop', 'Commercial Shop', 'Showroom', 'Godown',
                'Industrial Space', 'Chambers', 'Kiosk', 'Studio', 'Restaurants', 'Cafes', 'Hotel Space',
                'Hostel Guest House', 'Basement', 'Full Floor', 'Complete Building',
            ],
            'Agricultural' => ['Land', 'Agricultural Land', 'Farm House', 'FarmHouse Land'],
        ];

        $i = 0;
        foreach ($types as $parentName => $children) {
            $parent = PropertyType::firstOrCreate(['parent_id' => null, 'name' => $parentName], ['sort_order' => $i++]);
            foreach ($children as $j => $child) {
                PropertyType::firstOrCreate(['parent_id' => $parent->id, 'name' => $child], ['sort_order' => $j]);
            }
        }
    }
}

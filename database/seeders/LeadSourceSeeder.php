<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LeadSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            'Direct', 'Website', '99acres', 'Facebook', 'Housing.com', 'MagicBricks', 'Google Ads',
            'Instagram', 'YouTube', 'WhatsApp', 'Referral', 'Walk In', 'Channel Partner', 'QR Code',
            'IVR', 'Call', 'Data', 'MyGate', 'Excel Upload',
        ];

        foreach ($sources as $i => $name) {
            LeadSource::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'sort_order' => $i]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\LeadStage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Default lead stages and sub-stages, matching how the team works today.
 * Admins can rename, add or hide them later.
 */
class LeadStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            ['New', 'open', null, '#0d6efd', true, []],
            ['Callback', 'open', 'callback', '#20c997', false, [
                'Ringing', 'Busy', 'Not Answered', 'Not Reachable', 'Need More Info',
                'Follow Up', 'Plan Postponed', 'To Schedule Site Visit', 'To Schedule A Meeting',
            ]],
            ['Meeting Scheduled', 'open', 'meeting', '#6f42c1', false, ['Online', 'On Call', 'In Person', 'Others']],
            ['Site Visit Scheduled', 'open', 'site_visit', '#fd7e14', false, ['First Visit', 'Revisit', 'Cross Visit']],
            ['Pending', 'open', null, '#ffc107', false, []],
            ['Expression Of Interest', 'open', null, '#0dcaf0', false, []],
            ['Booked', 'won', null, '#198754', false, []],
            ['Invoiced', 'won', null, '#146c43', false, []],
            ['Booking Cancel', 'cancelled', null, '#6c757d', false, []],
            ['Not Interested', 'lost', null, '#dc3545', false, [
                'Unmatched Budget', 'Different Location', 'Different Requirements',
                'Not Responding', 'Not Looking For Property', 'Never Enquired',
            ]],
            ['Dropped', 'lost', null, '#b02a37', false, [
                'Not Looking', 'Purchased From Others', 'Wrong/Invalid No',
                'Ringing Not Received', 'Continuously Not Responding', 'Broker/CP',
            ]],
        ];

        foreach ($stages as $i => [$name, $outcome, $schedule, $color, $default, $children]) {
            $parent = LeadStage::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'outcome' => $outcome, 'schedule_type' => $schedule, 'color' => $color, 'is_default' => $default, 'sort_order' => $i]
            );

            foreach ($children as $j => $child) {
                LeadStage::updateOrCreate(
                    ['slug' => Str::slug($name.' '.$child)],
                    ['parent_id' => $parent->id, 'name' => $child, 'outcome' => $outcome, 'schedule_type' => $schedule, 'sort_order' => $j]
                );
            }
        }
    }
}

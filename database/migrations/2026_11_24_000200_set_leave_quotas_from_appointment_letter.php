<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Leave quotas as in the Keystone appointment letter (agreed 2026-11-24):
 *   12 paid leave a year (1 a month), up to 15 carried into the next year;
 *   12 casual/sick leave a year (1 a month), not carried forward.
 * Only touches the starter rows if HR hasn't changed them. Sick leave is switched off, not deleted,
 * so past sick-leave requests keep their type.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('leave_types')) {
            return;
        }
        $now = now();
        DB::table('leave_types')->where('code', 'CL')->where('name', 'Casual leave')->where('yearly_quota', 8)
            ->update(['name' => 'Casual / sick leave', 'yearly_quota' => 12, 'carry_forward_max' => 0, 'updated_at' => $now]);
        DB::table('leave_types')->where('code', 'SL')->where('name', 'Sick leave')->where('yearly_quota', 6)
            ->update(['active' => false, 'updated_at' => $now]);
        DB::table('leave_types')->where('code', 'EL')->where('name', 'Earned leave')->where('yearly_quota', 12)
            ->update(['name' => 'Paid leave', 'carry_forward_max' => 15, 'updated_at' => $now]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('leave_types')) {
            return;
        }
        DB::table('leave_types')->where('code', 'CL')->where('name', 'Casual / sick leave')->update(['name' => 'Casual leave', 'yearly_quota' => 8]);
        DB::table('leave_types')->where('code', 'SL')->where('name', 'Sick leave')->update(['active' => true]);
        DB::table('leave_types')->where('code', 'EL')->where('name', 'Paid leave')->update(['name' => 'Earned leave', 'carry_forward_max' => 12]);
    }
};

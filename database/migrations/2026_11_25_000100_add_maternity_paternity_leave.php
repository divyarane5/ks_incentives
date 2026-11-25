<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Maternity (30 paid days, women) and paternity (7 paid days, men) leave, after 12 months of service,
 * as in the appointment letter. Adds two rows only.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('leave_types')) {
            return;
        }
        $now = now();
        foreach ([
            ['Maternity leave', 'ML', 30, '#c2185b', 5],
            ['Paternity leave', 'PTL', 7, '#5e35b1', 6],
        ] as [$name, $code, $days, $color, $sort]) {
            if (! DB::table('leave_types')->where('code', $code)->exists()) {
                DB::table('leave_types')->insert([
                    'name' => $name, 'code' => $code, 'yearly_quota' => $days, 'is_paid' => true, 'carry_forward_max' => 0,
                    'color' => $color, 'active' => true, 'sort' => $sort, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('leave_types')->whereIn('code', ['ML', 'PTL'])->delete();
    }
};

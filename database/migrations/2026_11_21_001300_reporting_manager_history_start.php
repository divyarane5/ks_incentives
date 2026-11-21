<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reporting manager history: add a note column, and give every employee who has a
 * manager but no history yet a starting record (their current manager, from their
 * joining date; ended on their leaving date if they have left). Only adds; existing
 * history rows are not touched.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('employee_reporting_manager_histories', 'remarks')) {
            Schema::table('employee_reporting_manager_histories', function (Blueprint $table) {
                $table->string('remarks')->nullable()->after('changed_by');
            });
        }

        $users = DB::table('users')
            ->whereNotNull('reporting_manager_id')
            ->whereExists(fn ($q) => $q->select(DB::raw(1))->from('users as m')->whereColumn('m.id', 'users.reporting_manager_id'))
            ->whereNotExists(fn ($q) => $q->select(DB::raw(1))->from('employee_reporting_manager_histories as h')
                ->whereColumn('h.user_id', 'users.id'))
            ->get(['id', 'reporting_manager_id', 'joining_date', 'leaving_date', 'status', 'created_at']);

        $now = now();
        foreach ($users->chunk(200) as $chunk) {
            DB::table('employee_reporting_manager_histories')->insert($chunk->map(function ($u) use ($now) {
                $from = $u->joining_date ?: substr((string) $u->created_at, 0, 10) ?: $now->toDateString();
                $to = $u->status !== 'Active' && $u->leaving_date && $u->leaving_date >= $from ? $u->leaving_date : null;

                return [
                    'user_id' => $u->id,
                    'reporting_manager_id' => $u->reporting_manager_id,
                    'effective_from' => $from,
                    'effective_to' => $to,
                    'changed_by' => null,
                    'remarks' => 'Starting record: manager on file when history began',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })->values()->all());
        }
    }

    public function down(): void
    {
        DB::table('employee_reporting_manager_histories')
            ->where('remarks', 'Starting record: manager on file when history began')->delete();
        if (Schema::hasColumn('employee_reporting_manager_histories', 'remarks')) {
            Schema::table('employee_reporting_manager_histories', fn (Blueprint $t) => $t->dropColumn('remarks'));
        }
    }
};

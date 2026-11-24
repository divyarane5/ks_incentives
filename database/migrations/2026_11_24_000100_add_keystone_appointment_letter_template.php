<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds HR's full Keystone appointment letter (agreement, confidentiality agreement, Annexure I salary
 * table, Annexure II sales KRAs) as a letter template. Adds one row only; nothing else changes.
 */
return new class extends Migration
{
    private const NAME = 'Appointment letter – Keystone (full agreement)';

    public function up(): void
    {
        if (! Schema::hasTable('letter_templates') || DB::table('letter_templates')->where('name', self::NAME)->exists()) {
            return;
        }
        DB::table('letter_templates')->insert([
            'name' => self::NAME,
            'type' => 'appointment',
            'subject' => 'Appointment for the Post of {designation}',
            'body' => file_get_contents(resource_path('letters/appointment-keystone.txt')),
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('letter_templates')->where('name', self::NAME)->delete();
    }
};

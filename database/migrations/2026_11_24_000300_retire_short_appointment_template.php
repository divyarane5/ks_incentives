<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The short starter "Appointment letter" is switched off (not deleted) now that HR's full Keystone
 * appointment letter is a template, so HR doesn't pick the one-page version by mistake.
 * Only if HR hasn't edited it. Letters already issued with it are untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('letter_templates')) {
            return;
        }
        DB::table('letter_templates')->where('name', 'Appointment letter')->where('type', 'appointment')
            ->where('body', 'like', 'Dear {first_name},%Further to your acceptance of our offer%')
            ->update(['active' => false, 'name' => 'Appointment letter (short, old)', 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('letter_templates')->where('name', 'Appointment letter (short, old)')
            ->update(['active' => true, 'name' => 'Appointment letter']);
    }
};

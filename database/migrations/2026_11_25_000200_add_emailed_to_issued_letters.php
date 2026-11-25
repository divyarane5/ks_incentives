<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** When a letter was last emailed to the employee, and to which address. New columns only. */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('issued_letters') && ! Schema::hasColumn('issued_letters', 'emailed_at')) {
            Schema::table('issued_letters', function (Blueprint $table) {
                $table->timestamp('emailed_at')->nullable();
                $table->string('emailed_to')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('issued_letters', 'emailed_at')) {
            Schema::table('issued_letters', function (Blueprint $table) {
                $table->dropColumn(['emailed_at', 'emailed_to']);
            });
        }
    }
};

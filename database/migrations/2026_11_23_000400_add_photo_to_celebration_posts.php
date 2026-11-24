<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** A photo HR uploads just for the celebration card (instead of the profile photo). Adds one column. */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('celebration_posts') && ! Schema::hasColumn('celebration_posts', 'photo')) {
            Schema::table('celebration_posts', function (Blueprint $table) {
                $table->string('photo')->nullable()->after('message');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('celebration_posts', 'photo')) {
            Schema::table('celebration_posts', fn (Blueprint $table) => $table->dropColumn('photo'));
        }
    }
};

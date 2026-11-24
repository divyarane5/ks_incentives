<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** How the photo sits in the card's circle: "zoom,x,y" (e.g. "1.4,50,30"). Adds one column. */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('celebration_posts') && ! Schema::hasColumn('celebration_posts', 'photo_pos')) {
            Schema::table('celebration_posts', function (Blueprint $table) {
                $table->string('photo_pos', 40)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('celebration_posts', 'photo_pos')) {
            Schema::table('celebration_posts', fn (Blueprint $table) => $table->dropColumn('photo_pos'));
        }
    }
};

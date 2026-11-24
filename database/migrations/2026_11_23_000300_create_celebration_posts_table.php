<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Birthday and work anniversary posts HR puts on the Home dashboard. New table only. */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('celebration_posts')) {
            return;
        }
        Schema::create('celebration_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('type', 20); // birthday | anniversary
            $table->unsignedTinyInteger('design')->default(1); // 1-3
            $table->unsignedSmallInteger('years')->nullable(); // anniversaries
            $table->text('message')->nullable();
            $table->date('post_date')->index(); // shows on Home on this day
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'type', 'post_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('celebration_posts');
    }
};

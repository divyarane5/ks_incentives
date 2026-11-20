<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Auto-assignment: new leads with no owner go to the next person in a rule's pool, in turn.
 * A rule can be limited to one source and/or one project; the first matching rule wins.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->foreignId('source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->json('user_ids');
            $table->foreignId('last_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('assigned_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_rules');
    }
};

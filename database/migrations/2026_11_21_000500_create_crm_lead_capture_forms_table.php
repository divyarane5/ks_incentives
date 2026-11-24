<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website enquiry forms. Each has a secret link that a website (or the ready-made form page) posts leads to.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_capture_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('token', 40)->unique();
            $table->foreignId('source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('heading', 150)->nullable();
            $table->string('thank_you_url', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('leads_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_capture_forms');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per Excel/CSV lead upload (including the one-time Leadrat import), with progress and results.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_name');
            $table->string('path');
            // mapping, queued, running, done, failed
            $table->string('status', 20)->default('mapping')->index();
            $table->json('headers');
            $table->json('preview')->nullable();
            $table->json('mapping')->nullable();
            $table->json('options')->nullable();
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('processed_rows')->default(0);
            $table->unsignedInteger('created_count')->default(0);
            $table->unsignedInteger('re_enquiry_count')->default(0);
            $table->unsignedInteger('skipped_count')->default(0);
            $table->unsignedInteger('error_count')->default(0);
            // Rows that could not be imported: [row, name, phone, reason]
            $table->json('problems')->nullable();
            // Names in the file that did not match KeyLeads (owners, stages, sources, projects) and skip reasons
            $table->json('summary')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_imports');
    }
};

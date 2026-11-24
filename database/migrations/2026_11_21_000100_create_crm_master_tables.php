<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Editable lists: lead stages (with sub-stages), lead sources and property types.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('lead_stages')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('color', 9)->nullable();
            // open: still being worked, won: booked/invoiced, lost: dropped/not interested, cancelled
            $table->string('outcome', 20)->default('open');
            // callback, meeting, site_visit: picking this stage asks for a date and time
            $table->string('schedule_type', 20)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('lead_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('property_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('property_types')->cascadeOnDelete();
            $table->string('name', 100);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['parent_id', 'name']);
        });

        // Default stages, sources and property types (same as KeyLeads).
        (new \Database\Seeders\LeadStageSeeder)->run();
        (new \Database\Seeders\LeadSourceSeeder)->run();
        (new \Database\Seeders\PropertyTypeSeeder)->run();
    }

    public function down(): void
    {
        Schema::dropIfExists('property_types');
        Schema::dropIfExists('lead_sources');
        Schema::dropIfExists('lead_stages');
    }
};

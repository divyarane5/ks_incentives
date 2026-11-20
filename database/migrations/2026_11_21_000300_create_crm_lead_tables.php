<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Leads and everything that hangs off them: enquiry, linked projects/properties,
 * activity timeline, appointments and call logs.
 *
 * Indexes follow the filters the team uses every day (owner + stage, follow-up date,
 * source, phone) so lists stay fast at 500,000+ leads.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('serial_no', 20)->unique();
            $table->string('name', 150);
            $table->string('country_code', 5)->default('91');
            $table->string('phone', 20);
            $table->string('alt_phone', 20)->nullable();
            $table->string('email')->nullable();

            $table->foreignId('source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->string('sub_source', 100)->nullable();
            $table->string('campaign', 150)->nullable();

            $table->foreignId('stage_id')->constrained('lead_stages');
            $table->foreignId('sub_stage_id')->nullable()->constrained('lead_stages')->nullOnDelete();
            // hot, warm, cold
            $table->string('temperature', 10)->nullable();

            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('secondary_owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('picked_at')->nullable();

            $table->timestamp('next_follow_up_at')->nullable();
            // callback, meeting, site_visit
            $table->string('next_follow_up_type', 20)->nullable();
            $table->timestamp('last_contacted_at')->nullable();

            // A repeat enquiry from the same phone number is linked to the original lead.
            $table->foreignId('original_lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->unsignedSmallInteger('re_enquiry_count')->default(0);
            $table->timestamp('re_enquired_at')->nullable();

            // Closed leads are archived so they stop cluttering owners' lists and reports.
            $table->boolean('is_archived')->default(false);
            $table->string('legacy_id', 64)->nullable()->unique(); // id from Leadrat import

            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('phone');
            $table->index(['owner_id', 'is_archived', 'stage_id']);
            $table->index(['is_archived', 'next_follow_up_at']);
            $table->index(['source_id', 'created_at']);
            $table->index(['stage_id', 'sub_stage_id']);
            $table->index('created_at');
        });

        Schema::create('lead_enquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->unique()->constrained()->cascadeOnDelete();
            // buy, rent
            $table->string('enquired_for', 10)->default('buy');
            $table->foreignId('property_type_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('bhk', 3, 1)->nullable();
            $table->unsignedBigInteger('budget_min')->nullable();
            $table->unsignedBigInteger('budget_max')->nullable();
            $table->string('currency', 3)->default('INR');
            $table->string('locations', 500)->nullable();
            // investment, self_use
            $table->string('purpose', 20)->nullable();
            // immediate, within_6_months, within_1_year, under_construction
            $table->string('possession', 30)->nullable();
            $table->timestamps();
        });

        Schema::create('lead_project', function (Blueprint $table) {
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->primary(['lead_id', 'project_id']);
            $table->index('project_id');
        });

        Schema::create('lead_property', function (Blueprint $table) {
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->primary(['lead_id', 'property_id']);
            $table->index('property_id');
        });

        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // created, stage_changed, assigned, note, call, follow_up_set, appointment, re_enquiry, updated
            $table->string('type', 30);
            $table->string('from_value')->nullable();
            $table->string('to_value')->nullable();
            $table->text('body')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['lead_id', 'created_at']);
            $table->index(['user_id', 'type', 'created_at']);
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            // meeting, site_visit
            $table->string('type', 20);
            // online, on_call, in_person / first_visit, revisit, cross_visit
            $table->string('mode', 20)->nullable();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('scheduled_at');
            // scheduled, done, not_done, cancelled
            $table->string('status', 20)->default('scheduled');
            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('done_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('done_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['lead_id', 'scheduled_at']);
            $table->index(['type', 'status', 'scheduled_at']);
            $table->index(['assigned_to_id', 'scheduled_at']);
        });

        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // outgoing, incoming
            $table->string('direction', 10)->default('outgoing');
            // answered, busy, not_answered, not_reachable, wrong_number, switched_off
            $table->string('outcome', 20);
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('called_at');
            $table->timestamps();

            $table->index(['lead_id', 'called_at']);
            $table->index(['user_id', 'called_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_logs');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('lead_property');
        Schema::dropIfExists('lead_project');
        Schema::dropIfExists('lead_enquiries');
        Schema::dropIfExists('leads');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {

            $table->id();

            // Basic Details
            $table->date('booking_date')->nullable();
            $table->string('client_name');
            $table->string('client_contact')->nullable();
            $table->string('lead_source')->nullable();

            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('developer_id')->constrained()->cascadeOnDelete();
            $table->string('tower')->nullable();
            $table->string('wing')->nullable();
            $table->string('flat_no')->nullable();
            $table->string('configuration')->nullable();

            // Financial Values
            $table->decimal('booking_amount', 15, 2)->nullable();
            $table->decimal('agreement_value', 15, 2);

            // Brokerage Snapshot
            $table->decimal('base_brokerage_percent', 5, 2);
            $table->decimal('site_ladder_percent', 5, 2)->default(0);
            $table->decimal('aop_ladder_percent', 5, 2)->default(0);
            $table->decimal('total_brokerage_percent', 5, 2);

            $table->decimal('current_effective_amount', 15, 2);
            $table->decimal('total_paid_amount', 15, 2)->default(0);
            $table->decimal('pending_amount', 15, 2)->default(0);
            $table->decimal('additional_kicker', 15, 2)->default(0);
            $table->decimal('passback', 15, 2)->default(0);
            $table->decimal('final_revenue', 15, 2)->default(0);

    
            // Status
            $table->enum('booking_confirm', ['pending', 'approved', 'cancelled'])->default('pending');
            $table->boolean('registration_confirm')->default(false);
            $table->date('registration_date')->nullable();
            $table->boolean('invoice_raised')->default(false);

            $table->text('remark')->nullable();
            
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
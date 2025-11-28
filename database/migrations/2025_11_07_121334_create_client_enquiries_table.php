<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_enquiries', function (Blueprint $table) {
            $table->id();

            // Client Details
            $table->string('customer_name');
            $table->string('address')->nullable();
            $table->string('pin_code');
            $table->string('contact_no')->nullable();
            $table->string('alternate_no')->nullable();
            $table->string('email')->nullable();
            $table->string('profession')->nullable();
            $table->string('company_name')->nullable();
            $table->enum('residential_status', ['India', 'NRI'])->nullable();
            $table->string('nri_country')->nullable();

            // Channel Partner
            $table->unsignedBigInteger('channel_partner_id')->nullable();
            $table->foreign('channel_partner_id')->references('id')->on('channel_partners')->nullOnDelete();

            // Requirement Section
            $table->enum('property_type', ['Residential Flat', 'Commercial Office'])->nullable();
            $table->string('budget')->nullable();
            $table->enum('purchase_purpose', ['End Use', 'Investment', 'Gift'])->nullable();
            $table->enum('funding_source', ['Self', 'Loan', 'Both'])->nullable();

            // Source Section
            $table->boolean('team_call_received')->default(false);

            // ✅ Multiple Source of Visit options stored as JSON
            $table->string('source_of_visit')->nullable(); 
            
            // Reference info
            $table->string('reference_name')->nullable();
            $table->string('reference_contact')->nullable();
            
            // Closing Manager
            $table->unsignedBigInteger('closing_manager_id')->nullable();
            $table->foreign('closing_manager_id')->references('id')->on('users')->nullOnDelete();

            $table->unsignedBigInteger('presales_id')->nullable();
            $table->foreign('presales_id')->references('id')->on('users')->nullOnDelete();

            // Additional info
            $table->text('feedback')->nullable();

            $table->unsignedBigInteger('sourcing_manager_id')->nullable();
            $table->foreign('sourcing_manager_id')->references('id')->on('users')->nullOnDelete();
            $table->text('remarks')->nullable();

            // Meta
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_enquiries');
    }
};

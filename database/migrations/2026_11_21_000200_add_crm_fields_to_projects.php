<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * KeyLeads uses the same projects as bookings and brokerage. It only adds the details
 * sales needs (city, location, price range, ...) and the property inventory.
 * Developers stand in for KeyLeads "builders". Nothing existing is changed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // residential, commercial, agricultural
            $table->string('category', 20)->default('residential')->after('name');
            // upcoming, pre_launch, launch, ongoing, ready_to_move, resale
            $table->string('launch_status', 20)->nullable()->after('category');
            $table->string('location', 200)->nullable()->after('launch_status');
            $table->string('city', 100)->nullable()->index()->after('location');
            $table->unsignedBigInteger('min_price')->nullable()->after('city');
            $table->unsignedBigInteger('max_price')->nullable()->after('min_price');
            $table->date('possession_date')->nullable()->after('max_price');
            $table->text('description')->nullable()->after('possession_date');
        });

        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('property_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 200);
            // sale, rent
            $table->string('listing_for', 10)->default('sale')->index();
            $table->decimal('bhk', 3, 1)->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->decimal('carpet_area', 10, 2)->nullable();
            $table->string('area_unit', 20)->default('sq_ft');
            $table->string('unit_number', 50)->nullable();
            $table->string('location', 200)->nullable();
            $table->string('city', 100)->nullable()->index();
            // available, booked, sold
            $table->string('status', 20)->default('available')->index();
            $table->string('owner_name', 100)->nullable();
            $table->string('owner_phone', 20)->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['city']);
            $table->dropColumn(['category', 'launch_status', 'location', 'city', 'min_price', 'max_price', 'possession_date', 'description']);
        });
    }
};

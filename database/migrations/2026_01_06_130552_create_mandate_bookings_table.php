<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mandate_bookings', function (Blueprint $table) {
            $table->id();
            $table->date('booking_date');

            $table->foreignId('project_id')
                ->constrained('mandate_projects');

            $table->string('tower')->nullable();
            $table->string('wing')->nullable();
            $table->string('unit_no')->nullable();
            $table->string('floor_no')->nullable();
            $table->string('configuration')->nullable();
            $table->decimal('rera_carpet_area', 10, 2)->nullable();

            $table->integer('parking_count')->nullable();
            $table->enum('parking_type', ['Open','Covered'])->nullable();

            $table->enum('property_type', ['Residential','Commercial'])->nullable();

            $table->string('booking_form_file')->nullable();
            $table->string('booking_source')->nullable();

            $table->foreignId('channel_partner_id')
                ->nullable()
                ->constrained('channel_partners');

            $table->string('reference_name')->nullable();
            $table->string('reference_contact')->nullable();

            $table->text('source_remark')->nullable();
            $table->enum('booking_status', ['pending','completed','cancelled'])
            ->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mandate_bookings');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandateBookingApplicantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mandate_booking_applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                ->constrained('mandate_bookings')
                ->cascadeOnDelete();

            $table->enum('type', ['primary','co']);

            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();

            $table->string('mobile')->nullable();
            $table->string('alternate_mobile')->nullable();
            $table->string('email')->nullable();

            $table->string('pan_number')->nullable();
            $table->string('aadhar_number')->nullable();

            $table->string('pan_file')->nullable();
            $table->string('aadhar_file')->nullable();
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
        Schema::dropIfExists('mandate_booking_applicants');
    }
}

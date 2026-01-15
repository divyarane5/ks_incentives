<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandateBookingSignaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mandate_booking_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                ->constrained('mandate_bookings')
                ->cascadeOnDelete();

            $table->string('developer_consent_file')->nullable();
            $table->string('mandate_consent_file')->nullable();
            $table->string('cp_consent_file')->nullable();
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
        Schema::dropIfExists('mandate_booking_signatures');
    }
}

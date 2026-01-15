<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandateBookingAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mandate_booking_addresses', function (Blueprint $table) {
        $table->engine = 'InnoDB';

        $table->id();
        $table->unsignedBigInteger('applicant_id');

        $table->enum('address_type', ['communication', 'permanent']);
        $table->text('address')->nullable();
        $table->string('city')->nullable();
        $table->string('state')->nullable();
        $table->string('pincode')->nullable();

        $table->timestamps();

        $table->foreign('applicant_id')
            ->references('id')
            ->on('mandate_booking_applicants')
            ->onDelete('cascade');
    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mandate_booking_addresses');
    }
}

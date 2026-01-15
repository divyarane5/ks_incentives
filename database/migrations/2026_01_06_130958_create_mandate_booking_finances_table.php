<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandateBookingFinancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mandate_booking_finances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                ->constrained('mandate_bookings')
                ->cascadeOnDelete();

            $table->decimal('unit_value', 15, 2)->nullable();
            $table->decimal('other_charges', 15, 2)->nullable();
            $table->decimal('car_park_charges', 15, 2)->nullable();

            $table->decimal('agreement_value', 15, 2)->nullable();

            $table->decimal('current_due_percent', 5, 2)->nullable();

            /* IMPORTANT */
            $table->boolean('is_registered')->default(false);

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
        Schema::dropIfExists('mandate_booking_finances');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandateBookingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mandate_booking_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                ->constrained('mandate_bookings')
                ->cascadeOnDelete();

            $table->decimal('amount', 15, 2);

            $table->enum('mode', [
                'UPI',
                'Card',
                'NetBanking',
                'Cheque',
                'CC'
            ]);

            $table->date('date')->nullable();
            $table->string('bank_name')->nullable();

            $table->string('transaction_id')->nullable();
            $table->string('cheque_number')->nullable();
            $table->string('proof')->nullable();

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
        Schema::dropIfExists('mandate_booking_payments');
    }
}

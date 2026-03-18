<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_brokerage_payments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('booking_id')
                  ->constrained('bookings')
                  ->cascadeOnDelete();

            // Invoice Details
            $table->decimal('invoice_percent',5,2)->default(0);
            $table->decimal('invoice_amount',15,2)->default(0);

            $table->date('invoice_date')->nullable();

            $table->string('invoice_file')->nullable();

            // Bank Payment
            $table->decimal('bank_received_amount',15,2)->default(0);

            $table->date('bank_received_date')->nullable();

            // Status
            $table->enum('status',['invoice_raised','received'])
                  ->default('invoice_raised');

            // Notes
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_brokerage_payments');
    }
};
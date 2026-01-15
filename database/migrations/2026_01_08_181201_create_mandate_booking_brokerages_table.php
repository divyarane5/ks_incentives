<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandateBookingBrokeragesTable extends Migration
{
    public function up()
    {
        Schema::create('mandate_booking_brokerages', function (Blueprint $table) {

            $table->id();

            $table->foreignId('booking_id')
                ->constrained('mandate_bookings')
                ->cascadeOnDelete();

            /* ===== Eligibility Snapshot ===== */

            $table->decimal('agreement_value', 15, 2)->nullable();
            $table->decimal('total_paid', 15, 2)->nullable();
            $table->decimal('payment_percent', 5, 2)->nullable();

            $table->decimal('threshold_percentage', 5, 2)->nullable();
            $table->decimal('current_due_percentage', 5, 2)->nullable();

            $table->boolean('is_registered')->default(false);

            /* ===== Eligibility Result ===== */

            $table->boolean('is_eligible')->default(false);
            $table->string('eligibility_scenario')->nullable(); 
            // scenario_1 | scenario_2

            $table->text('eligibility_reason')->nullable();

            /* ===== Brokerage Amount ===== */

            $table->decimal('brokerage_percent', 5, 2)->nullable();
            $table->decimal('brokerage_amount', 15, 2)->nullable();

            /* ===== Lifecycle ===== */

            $table->enum('status', [
                'pending',
                'eligible',
                'approved',
                'paid'
            ])->default('pending');

            $table->timestamp('eligible_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mandate_booking_brokerages');
    }
}

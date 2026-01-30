<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandateBrokerageLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mandate_booking_brokerage_ledgers', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('brokerage_id'); // mandate_booking_brokerages.id
            $table->unsignedBigInteger('channel_partner_id')->nullable();

            // Party
            $table->enum('party_type', ['CP', 'OUR']);

            // Financials
            $table->decimal('brokerage_percent', 5, 2);
            $table->decimal('brokerage_amount', 15, 2);

            // Ledger intent
            $table->enum('entry_type', [
                'release',
                'retention',
                'adjustment'
            ]);

            // Calculation context
            $table->enum('calculation_type', [
                'initial',
                'ladder_upgrade',
                'ladder_downgrade',
                'manual_adjustment'
            ])->default('initial');

            // Payment details
            $table->enum('payment_mode', [
                'NEFT',
                'RTGS',
                'UPI',
                'CHEQUE',
                'INTERNAL'
            ])->nullable();

            $table->string('reference_no')->nullable();
            $table->date('payment_date')->nullable();

            // Status & locking
            $table->enum('status', [
                'pending',
                'paid',
                'reversed'
            ])->default('pending');

            $table->boolean('is_locked')->default(false);

            // Meta
            $table->text('remark')->nullable();
            $table->timestamp('effective_from')->nullable();

            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('booking_id');
            $table->index('brokerage_id');
            $table->index('party_type');
            $table->index('channel_partner_id');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mandate_booking_brokerage_ledgers');
    }
}

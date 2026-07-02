<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActualReceiptAmountToBookingBrokeragePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_brokerage_payments', function (Blueprint $table) {

            $table->decimal('actual_receipt_amount', 15, 2)
                  ->nullable()
                  ->after('bank_received_amount');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_brokerage_payments', function (Blueprint $table) {

            $table->dropColumn('actual_receipt_amount');

        });
    }
}

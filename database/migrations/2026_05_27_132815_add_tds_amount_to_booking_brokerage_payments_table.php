<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTdsAmountToBookingBrokeragePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_brokerage_payments', function (Blueprint $table) {

            $table->decimal('tds_amount',15,2)
                ->default(0)
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
            //
        });
    }
}

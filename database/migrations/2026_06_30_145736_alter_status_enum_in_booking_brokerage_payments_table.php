<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStatusEnumInBookingBrokeragePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::statement("
            ALTER TABLE booking_brokerage_payments
            MODIFY status ENUM(
                'invoice_raised',
                'partial',
                'received'
            ) NOT NULL DEFAULT 'invoice_raised'
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            ALTER TABLE booking_brokerage_payments
            MODIFY status ENUM(
                'invoice_raised',
                'received'
            ) NOT NULL DEFAULT 'invoice_raised'
        ");
    }
}

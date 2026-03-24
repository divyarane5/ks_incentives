<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBookingBrokerageDefaults extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {

            $table->decimal('base_brokerage_percent',5,2)->default(0)->change();
            $table->decimal('site_ladder_percent',5,2)->default(0)->change();
            $table->decimal('aop_ladder_percent',5,2)->default(0)->change();
            $table->decimal('total_brokerage_percent',5,2)->default(0)->change();

            $table->decimal('current_effective_amount',15,2)->default(0)->change();
            $table->decimal('final_revenue',15,2)->default(0)->change();
        });
    }

    public function down()
    {
        //
    }
}
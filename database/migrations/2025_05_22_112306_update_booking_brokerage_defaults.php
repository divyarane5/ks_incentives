<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBookingBrokerageDefaults extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {

            // ADD if missing
            if (!Schema::hasColumn('bookings', 'base_brokerage_percent')) {
                $table->decimal('base_brokerage_percent',5,2)->default(0);
            }

            if (!Schema::hasColumn('bookings', 'site_ladder_percent')) {
                $table->decimal('site_ladder_percent',5,2)->default(0);
            }

            if (!Schema::hasColumn('bookings', 'aop_ladder_percent')) {
                $table->decimal('aop_ladder_percent',5,2)->default(0);
            }

            if (!Schema::hasColumn('bookings', 'total_brokerage_percent')) {
                $table->decimal('total_brokerage_percent',5,2)->default(0);
            }

            if (!Schema::hasColumn('bookings', 'current_effective_amount')) {
                $table->decimal('current_effective_amount',15,2)->default(0);
            }

            if (!Schema::hasColumn('bookings', 'final_revenue')) {
                $table->decimal('final_revenue',15,2)->default(0);
            }

        });
    }

    public function down(){}
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('sales_person');
            $table->date('booking_date')->nullable();
            $table->string('client_name');
            $table->string('client_contact');
            $table->string('project_id');
            $table->string('developer_id');
            $table->string('lead_source');
            $table->string('sourcing_manager');
            $table->string('sourcing_contact');
            $table->string('tower');
            $table->string('configuration');
            $table->string('flat_no');
            $table->string('wing');
            $table->string('booking_amount')->nullable();
            $table->string('agreement_value');
            $table->string('passback')->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}


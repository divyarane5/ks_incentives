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
            $table->string('project_name');
            $table->string('developer_name');
            $table->string('developer_email');
            $table->string('client_name');
            $table->date('booking_date')->nullable();
            $table->string('configuration');
            $table->string('flat_no');
            $table->string('wing');
            $table->string('tower');
            $table->string('sales_person');
            $table->string('sourcing_manager');
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


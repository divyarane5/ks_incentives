<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncentiveCalculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incentive_calculations', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id');

            $table->string('financial_year');

            $table->string('role');

            $table->decimal('annual_salary', 15, 2)->default(0);

            $table->decimal('booking_collection', 15, 2)->default(0);

            $table->decimal('performance_times', 15, 2)->default(0);

            $table->decimal('justification_amount', 15, 2)->default(0);

            $table->decimal('eligible_collection', 15, 2)->default(0);

            $table->decimal('incentive_percent', 10, 2)->default(0);

            $table->decimal('final_incentive', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incentive_calculations');
    }
}

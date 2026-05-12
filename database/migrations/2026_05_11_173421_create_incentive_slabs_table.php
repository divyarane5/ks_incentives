<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncentiveSlabsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incentive_slabs', function (Blueprint $table) {

            $table->id();

            $table->string('financial_year'); // 2025-26

            $table->string('role'); // FOS

            $table->decimal('from_times', 10, 2)->default(0);

            $table->decimal('to_times', 10, 2)->nullable();

            $table->decimal('incentive_percent', 10, 2)->default(0);

            $table->decimal('justification_multiplier', 10, 2)->default(4);

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
        Schema::dropIfExists('incentive_slabs');
    }
}

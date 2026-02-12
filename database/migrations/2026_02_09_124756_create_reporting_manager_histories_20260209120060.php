<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportingManagerHistories20260209120060 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('employee_reporting_manager_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('reporting_manager_id');

            $table->date('effective_from');
            $table->date('effective_to')->nullable();

            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id', 'ermh_user_fk')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('reporting_manager_id', 'ermh_manager_fk')->references('id')->on('users')->cascadeOnDelete();

            $table->index(['user_id', 'effective_from'], 'ermh_user_effective_idx');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reporting_manager_histories_20260209120060');
    }
}

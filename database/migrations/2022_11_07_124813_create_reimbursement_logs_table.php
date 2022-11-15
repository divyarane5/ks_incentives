<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReimbursementLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reimbursement_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reimbursement_id')->index();
            $table->foreign('reimbursement_id')->references('id')->on('reimbursements');
            $table->enum('status', ['pending', 'approved', 'rejected', 'settled'])->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('created_by');
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
        Schema::dropIfExists('reimbursement_logs');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReimbursementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();
            $table->string('client_name')->nullable();
            $table->string('project_name')->nullable();
            $table->unsignedBigInteger('visit_attended_of_id')->index();
            $table->foreign('visit_attended_of_id')->references('id')->on('users');
            $table->string('source')->nullable();
            $table->string('destination')->nullable();
            $table->string('transport_mode')->nullable();
            $table->integer('amount');
            $table->integer('settlement_amount')->default(0);
            $table->text('settlement_comment')->nullable();
            $table->text('comment')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'settled'])->default('pending')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedInteger('created_by');
            $table->softDeletes();
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
        Schema::dropIfExists('reimbursements');
    }
}

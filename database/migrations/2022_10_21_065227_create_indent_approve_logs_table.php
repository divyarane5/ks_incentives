<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndentApproveLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indent_approve_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('indent_id')->index();
            $table->foreign('indent_id')->references('id')->on('indents')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedFloat('status')->comment('0-pending, 1-approved, 2-rejected, 1.1-approve1, 1.2-approve2, 1.3-approve3, 1.4-approve4, 1.5-approve5');
            $table->longText('description')->nullable();
            $table->date('submission_date');
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
        Schema::dropIfExists('indent_approve_logs');
    }
}

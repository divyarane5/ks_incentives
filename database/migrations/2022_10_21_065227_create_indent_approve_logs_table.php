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
            $table->unsignedBigInteger('indent_item_id')->index();
            $table->foreign('indent_item_id')->references('id')->on('indent_items')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->enum('status', ['pending', 'approved', 'rejected', 'approve1', 'approve2', 'approve3', 'approve4', 'approve5']);
            $table->longText('description')->nullable();
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

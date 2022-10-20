<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndentConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indent_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('expense_id')->index();
            $table->foreign('expense_id')->references('id')->on('expenses');
            $table->unsignedInteger('approver1')->nullable();
            $table->unsignedInteger('approver2')->nullable();
            $table->unsignedInteger('approver3')->nullable();
            $table->unsignedInteger('approver4')->nullable();
            $table->unsignedInteger('approver5')->nullable();
            $table->unsignedInteger('monthly_limit')->nullable()->comment('Limit in INR');
            $table->unsignedInteger('indent_limit')->nullable()->comment('Limit in INR');
            $table->tinyInteger('monthly_limit_approval_required')->nullable()->default(0);
            $table->tinyInteger('indent_limit_approval_required')->nullable()->default(0);
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
        Schema::dropIfExists('indent_configurations');
    }
}

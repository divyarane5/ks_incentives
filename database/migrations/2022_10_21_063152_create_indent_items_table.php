<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indent_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('indent_id')->index();
            $table->foreign('indent_id')->references('id')->on('indents')->onDelete('cascade');
            $table->unsignedBigInteger('expense_id')->index();
            $table->foreign('expense_id')->references('id')->on('expenses');
            $table->unsignedBigInteger('vendor_id')->index();
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('unit_price');
            $table->unsignedDouble('gst');
            $table->unsignedDouble('tds');
            $table->unsignedDouble('total');
            $table->enum('status', ['pending', 'approved', 'rejected', 'approve1', 'approve2', 'approve3', 'approve4', 'approve5'])->default('pending');
            $table->string('next_approver_id')->default(0)->nullable();
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
        Schema::dropIfExists('indent_items');
    }
}

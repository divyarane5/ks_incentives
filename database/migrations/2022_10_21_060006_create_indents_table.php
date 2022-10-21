<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('location_id')->index();
            $table->foreign('location_id')->references('id')->on('locations');
            $table->unsignedBigInteger('business_unit_id')->index();
            $table->foreign('business_unit_id')->references('id')->on('business_units');
            $table->enum('bill_mode', ['advanced', 'partial', 'against'])->nullable();
            $table->date('softcopy_bill_submission_date')->nullable();
            $table->date('hardcopy_bill_submission_date')->nullable();
            $table->unsignedInteger('total');
            $table->unsignedFloat('status')->comment('0-pending, 1-approved, 2-rejected, 1.1-approve1, 1.2-approve2, 1.3-approve3, 1.4-approve4, 1.5-approve5');
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
        Schema::dropIfExists('indents');
    }
}

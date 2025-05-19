<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeveloperLaddersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('developer_ladders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('developer_id')->index();
            $table->foreign('developer_id')->references('id')->on('developers');
            $table->string('aop');
            $table->string('ladder');
            $table->date('aop_s_date');
            $table->date('aop_e_date');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('developer_ladders');
    }
}

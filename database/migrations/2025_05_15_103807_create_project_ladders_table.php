<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectLaddersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_ladders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->index();
            $table->foreign('project_id')->references('id')->on('projects');
            $table->unsignedBigInteger('aop_id')->index();
            $table->foreign('aop_id')->references('id')->on('developer_ladders');
            $table->string('booking');
            $table->string('ladder');
            $table->date('project_s_date');
            $table->date('project_e_date');
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
        Schema::dropIfExists('project_ladders');
    }
}

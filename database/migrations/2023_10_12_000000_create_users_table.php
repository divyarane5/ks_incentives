<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('employee_code')->index();
            $table->string('entity');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('gender');
            $table->date('dob');
            $table->unsignedBigInteger('location_id')->index();
            $table->foreign('location_id')->references('id')->on('locations');
            $table->unsignedBigInteger('department_id')->index();
            $table->foreign('department_id')->references('id')->on('departments');
            $table->unsignedBigInteger('designation_id')->index();
            $table->foreign('designation_id')->references('id')->on('designations');
            $table->unsignedBigInteger('reporting_user_id')->index();
            $table->foreign('reporting_user_id')->references('id')->on('users');
            $table->tinyInteger('status')->default(1);
            $table->dateTime('last_login');
            $table->unsignedBigInteger('created_by');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJoiningFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('joining_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_id')->index();
            $table->foreign('candidate_id')->references('id')->on('candidates');
            $table->date('joining_date')->nullable();
            $table->string('designation')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->date('dob')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('aadhar_number')->nullable();
            $table->string('gender')->nullable();
            $table->string('photo')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->longText('educational_details')->nullable();
            $table->longText('organizational_details')->nullable();
            $table->longText('family_details')->nullable();
            $table->longText('professional_details')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc')->nullable();
            $table->text('suffered_from_disease')->nullable();
            $table->text('practitioner_details')->nullable();
            $table->text('convicted_in_law')->nullable();
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
        Schema::dropIfExists('joining_forms');
    }
}

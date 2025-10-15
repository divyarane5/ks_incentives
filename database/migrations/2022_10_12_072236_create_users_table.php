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
            $table->string('employee_code')->nullable()->unique();
            $table->string('entity')->nullable();
            $table->string('title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name')->nullable();
            $table->string('gender')->nullable();
            $table->string('photo')->nullable();
            $table->string('status')->default('Onboarding');

            // Contact
            $table->string('official_contact')->nullable();
            $table->string('personal_contact')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('official_email')->nullable()->unique();
            $table->string('personal_email')->nullable();

            // Employment
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedBigInteger('reporting_manager_id')->nullable();
            $table->unsignedBigInteger('work_location_id')->nullable();
            $table->string('location_handled')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('confirm_date')->nullable();
            $table->date('leaving_date')->nullable();
            $table->string('exit_status')->nullable();
            $table->text('reason_for_leaving')->nullable();
            $table->string('fnf_status')->nullable();

            // Salary & Compensation
            $table->decimal('current_ctc', 12, 2)->nullable();
            $table->decimal('monthly_basic', 12, 2)->nullable();
            $table->decimal('monthly_hra', 12, 2)->nullable();
            $table->decimal('special_allowance', 12, 2)->nullable();
            $table->decimal('conveyance_allowance', 12, 2)->nullable();
            $table->decimal('medical_reimbursement', 12, 2)->nullable();
            $table->decimal('professional_tax', 12, 2)->nullable();
            $table->decimal('pf_employer', 12, 2)->nullable();
            $table->decimal('pf_employee', 12, 2)->nullable();
            $table->decimal('net_deductions', 12, 2)->nullable();
            $table->decimal('net_salary', 12, 2)->nullable();

            // Statutory & Banking
            $table->boolean('pf_status')->default(false);
            $table->date('pf_joining_date')->nullable();
            $table->string('uan_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('bank_account_number')->nullable();

            // Personal & Emergency
            $table->date('dob')->nullable();
            $table->integer('age')->nullable();
            $table->string('birthday_month')->nullable();
            $table->string('blood_group')->nullable();
            $table->text('communication_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('languages_known')->nullable();
            $table->string('education_qualification')->nullable();
            $table->string('marital_status')->nullable();
            $table->date('marriage_date')->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('parents_contact')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('aadhar_no')->nullable();

            // Assets & Misc
            $table->string('laptop_desktop')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_sim')->nullable();
            $table->string('work_off')->nullable();
            $table->text('additional_comments')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            // Auth
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->dateTime('last_login')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('set null');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('reporting_manager_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('work_location_id')->references('id')->on('locations')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

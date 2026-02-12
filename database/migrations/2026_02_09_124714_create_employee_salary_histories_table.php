<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeSalaryHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('employee_salary_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Salary fields
            $table->decimal('annual_ctc', 12, 2);
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

            // Optional fields
            $table->string('change_reason')->nullable(); // Increment, Promotion, Onboarding
            $table->date('effective_from');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->index(['user_id', 'effective_from']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_salary_histories');
    }
}

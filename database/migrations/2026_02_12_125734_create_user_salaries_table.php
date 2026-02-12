<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('user_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('financial_year');
            $table->string('month');

            $table->decimal('gross_salary', 12, 2)->default(0);
            $table->decimal('professional_tax', 12, 2)->default(0);
            $table->decimal('pf_amount', 12, 2)->default(0);
            $table->decimal('extra_deduction', 12, 2)->default(0);

            $table->decimal('system_net_salary', 12, 2)->default(0);
            $table->decimal('salary_credited', 12, 2)->default(0);
            $table->decimal('total_employee_cost', 12, 2)->default(0);

            $table->string('status')->default('pending');
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'financial_year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_salaries');
    }
}

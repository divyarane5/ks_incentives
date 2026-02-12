<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeExitHistories20260209120050 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_exit_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Exit details
            $table->date('exit_date')->nullable();
            $table->string('exit_type')->nullable();
            // Resignation | Termination | Absconded | Contract End

            $table->string('exit_reason')->nullable();
            $table->text('remarks')->nullable();

            // HR approvals
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Rehire support
            $table->boolean('is_rehirable')->default(true);

            $table->timestamps();

            $table->index(['user_id', 'exit_date']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_exit_histories_20260209120050');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * KeyPeople leave: leave types, holidays, leave requests and balance adjustments.
 * New tables only; nothing existing is changed. Leave year = financial year (April to March).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('leave_types')) {
            Schema::create('leave_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code', 10);
                $table->decimal('yearly_quota', 5, 1)->default(0);   // days per financial year
                $table->boolean('is_paid')->default(true);            // unpaid = loss of pay, no balance
                $table->decimal('carry_forward_max', 5, 1)->default(0);
                $table->string('color', 20)->nullable();
                $table->boolean('active')->default(true);
                $table->unsignedSmallInteger('sort')->default(0);
                $table->timestamps();
            });

            // Common starting point; HR changes these on KeyPeople > Leave setup.
            $now = now();
            DB::table('leave_types')->insert([
                ['name' => 'Casual leave', 'code' => 'CL', 'yearly_quota' => 8, 'is_paid' => true, 'carry_forward_max' => 0, 'color' => '#3b6fb6', 'active' => true, 'sort' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Sick leave', 'code' => 'SL', 'yearly_quota' => 6, 'is_paid' => true, 'carry_forward_max' => 0, 'color' => '#1a7f3c', 'active' => true, 'sort' => 2, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Earned leave', 'code' => 'EL', 'yearly_quota' => 12, 'is_paid' => true, 'carry_forward_max' => 12, 'color' => '#c3b246', 'active' => true, 'sort' => 3, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Unpaid leave', 'code' => 'LWP', 'yearly_quota' => 0, 'is_paid' => false, 'carry_forward_max' => 0, 'color' => '#b42318', 'active' => true, 'sort' => 9, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (! Schema::hasTable('holidays')) {
            Schema::create('holidays', function (Blueprint $table) {
                $table->id();
                $table->date('date');
                $table->string('name');
                $table->unsignedBigInteger('location_id')->nullable(); // empty = all offices
                $table->timestamps();
                $table->index('date');
            });
        }

        if (! Schema::hasTable('leave_requests')) {
            Schema::create('leave_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('leave_type_id');
                $table->date('from_date');
                $table->date('to_date');
                $table->string('half_day', 10)->nullable();   // null, first, second (single-day only)
                $table->decimal('days', 5, 1);
                $table->text('reason')->nullable();
                $table->string('status', 15)->default('pending'); // pending, approved, rejected, cancelled
                $table->unsignedBigInteger('approver_id')->nullable();  // who should approve (manager)
                $table->unsignedBigInteger('decided_by')->nullable();
                $table->timestamp('decided_at')->nullable();
                $table->string('decision_note', 255)->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'from_date']);
                $table->index(['approver_id', 'status']);
            });
        }

        if (! Schema::hasTable('leave_adjustments')) {
            Schema::create('leave_adjustments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('leave_type_id');
                $table->string('financial_year', 7);   // 2026-27
                $table->decimal('days', 5, 1);         // + adds to balance, - takes away
                $table->string('note', 255)->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'financial_year']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_adjustments');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('leave_types');
    }
};

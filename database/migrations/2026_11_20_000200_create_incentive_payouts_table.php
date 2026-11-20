<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Quarterly incentive ledger. Each quarter is worked out on the year so far and frozen
 * once approved; the next quarter pays the new year-so-far figure minus what was frozen before.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incentive_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('financial_year', 7);          // 2025-26
            $table->unsignedTinyInteger('quarter');       // 1 = Apr-Jun ... 4 = Jan-Mar
            $table->string('role', 20);                   // FOS, TL, Sr. TL, CH
            $table->decimal('ytd_salary', 15, 2)->default(0);
            $table->decimal('ytd_collection', 15, 2)->default(0);
            $table->decimal('performance_times', 10, 2)->default(0);
            $table->decimal('justification_multiplier', 10, 2)->default(0);
            $table->decimal('justification_amount', 15, 2)->default(0);
            $table->decimal('incentive_base', 15, 2)->default(0); // FOS: collection above justification; managers: team FOS incentive
            $table->decimal('slab_percent', 10, 2)->default(0);
            $table->decimal('ytd_incentive', 15, 2)->default(0);
            $table->decimal('previously_frozen', 15, 2)->default(0);
            $table->decimal('payable', 15, 2)->default(0);
            $table->string('note')->nullable();
            $table->json('details')->nullable();
            $table->foreignId('frozen_by')->nullable()->constrained('users');
            $table->timestamp('frozen_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'financial_year', 'quarter', 'role'], 'incentive_payouts_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incentive_payouts');
    }
};

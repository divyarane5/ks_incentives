<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_salaries', function (Blueprint $table) {
            $table->id();

            // Employee
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Financial year start (e.g. 2024 = Apr 2024 – Mar 2025)
            $table->year('financial_year');

            // Month number (1 = Jan ... 12 = Dec)
            $table->unsignedTinyInteger('month');

            // Salary actually credited
            $table->decimal('credited_amount', 10, 2)->default(0);

            // Date salary credited in bank
            $table->date('credited_on')->nullable();

            // Optional notes
            $table->string('remarks')->nullable();

            // Who added/updated this entry
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();

            // Prevent duplicate salary entries for same month
            $table->unique(['user_id', 'financial_year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_salaries');
    }
};

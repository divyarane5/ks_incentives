<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixUserSalariesFy extends Migration
{
    public function up()
    {
        Schema::table('user_salaries', function (Blueprint $table) {

            // FY label like "2024-25"
            $table->string('financial_year_label', 7)
                  ->nullable()
                  ->after('user_id');

            // Actual calendar year (2024 / 2025)
            $table->year('salary_year')
                  ->nullable()
                  ->after('financial_year_label');

            // Optional index for faster queries
            $table->index(['user_id', 'financial_year_label', 'month']);
        });
    }

    public function down()
    {
        Schema::table('user_salaries', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'financial_year_label', 'month']);
            $table->dropColumn(['financial_year_label', 'salary_year']);
        });
    }
}

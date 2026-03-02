<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBaseBrokerageAndReraToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {

            $table->decimal('base_brokerage_percent', 5, 2)
                ->default(0)
                ->after('name');

            $table->string('rera_number')
                ->nullable()
                ->after('base_brokerage_percent');

        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'base_brokerage_percent',
                'rera_number'
            ]);
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mandate_projects', function (Blueprint $table) {

            // Threshold Percentage
            $table->decimal('threshold_percentage', 5, 2)
                  ->nullable()
                  ->after('property_type');

            // Brokerage Criteria
            $table->string('brokerage_criteria', 20)
                  ->after('threshold_percentage')
                  ->comment('AV | UCV_OCC | UCV_CPC');

        });
    }

    public function down(): void
    {
        Schema::table('mandate_projects', function (Blueprint $table) {
            $table->dropColumn([
                'threshold_percentage',
                'brokerage_criteria',
            ]);
        });
    }
};

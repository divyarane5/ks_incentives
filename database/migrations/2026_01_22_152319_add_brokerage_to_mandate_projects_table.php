<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('mandate_projects', function (Blueprint $table) {
            $table->decimal('brokerage', 5, 2)
                  ->nullable()
                  ->after('threshold_percentage')
                  ->comment('Brokerage percentage');
        });
    }

    public function down()
    {
        Schema::table('mandate_projects', function (Blueprint $table) {
            $table->dropColumn('brokerage');
        });
    }
};

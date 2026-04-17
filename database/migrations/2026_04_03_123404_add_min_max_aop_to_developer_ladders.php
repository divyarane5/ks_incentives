<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinMaxAopToDeveloperLadders extends Migration
{
    public function up()
    {
        Schema::table('developer_ladders', function (Blueprint $table) {

            if (!Schema::hasColumn('developer_ladders', 'min_aop')) {
                $table->decimal('min_aop', 15, 2)
                      ->nullable()
                      ->after('developer_id');
            }

            if (!Schema::hasColumn('developer_ladders', 'max_aop')) {
                $table->decimal('max_aop', 15, 2)
                      ->nullable()
                      ->after('min_aop');
            }

        });
    }

    public function down()
    {
        Schema::table('developer_ladders', function (Blueprint $table) {

            if (Schema::hasColumn('developer_ladders', 'min_aop')) {
                $table->dropColumn('min_aop');
            }

            if (Schema::hasColumn('developer_ladders', 'max_aop')) {
                $table->dropColumn('max_aop');
            }

        });
    }
}
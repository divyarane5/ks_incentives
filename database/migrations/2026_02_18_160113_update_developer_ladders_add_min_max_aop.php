<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDeveloperLaddersAddMinMaxAop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ⛔ Prevent duplicate execution
        if (
            Schema::hasColumn('developer_ladders', 'min_aop') &&
            Schema::hasColumn('developer_ladders', 'max_aop')
        ) {
            return;
        }

        Schema::table('developer_ladders', function (Blueprint $table) {

            if (!Schema::hasColumn('developer_ladders', 'min_aop')) {
                $table->decimal('min_aop', 15, 2)->nullable()->after('developer_id');
            }

            if (!Schema::hasColumn('developer_ladders', 'max_aop')) {
                $table->decimal('max_aop', 15, 2)->nullable()->after('min_aop');
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

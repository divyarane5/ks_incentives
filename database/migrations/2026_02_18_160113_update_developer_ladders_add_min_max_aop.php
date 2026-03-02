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
        Schema::table('developer_ladders', function (Blueprint $table) {
            $table->decimal('min_aop', 15, 2)->nullable()->after('developer_id');
            $table->decimal('max_aop', 15, 2)->nullable()->after('min_aop');
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

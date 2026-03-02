<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDeveloperIdFromProjectLaddersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::table('project_ladders', function (Blueprint $table) {

            // Drop foreign key first
            $table->dropForeign(['developer_id']);

            // Then drop column
            $table->dropColumn('developer_id');
        });
    }

    public function down()
    {
        Schema::table('project_ladders', function (Blueprint $table) {
            $table->unsignedBigInteger('developer_id')->nullable();
        });
    }
}

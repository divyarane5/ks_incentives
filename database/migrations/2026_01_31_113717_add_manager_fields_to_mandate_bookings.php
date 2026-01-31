<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('mandate_bookings', function (Blueprint $table) {

            // 1️⃣ created_by
            $table->unsignedBigInteger('created_by')
                  ->nullable()
                  ->after('id');

            // 2️⃣ manager fields
            $table->unsignedBigInteger('closing_manager_id')
                  ->nullable()
                  ->after('created_by');

            $table->unsignedBigInteger('presales_id')
                  ->nullable()
                  ->after('closing_manager_id');

            $table->unsignedBigInteger('sourcing_manager_id')
                  ->nullable()
                  ->after('presales_id');

            // 3️⃣ foreign keys
            $table->foreign('created_by')
                  ->references('id')->on('users')
                  ->nullOnDelete();

            $table->foreign('closing_manager_id')
                  ->references('id')->on('users')
                  ->nullOnDelete();

            $table->foreign('presales_id')
                  ->references('id')->on('users')
                  ->nullOnDelete();

            $table->foreign('sourcing_manager_id')
                  ->references('id')->on('users')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('mandate_bookings', function (Blueprint $table) {

            $table->dropForeign(['created_by']);
            $table->dropForeign(['closing_manager_id']);
            $table->dropForeign(['presales_id']);
            $table->dropForeign(['sourcing_manager_id']);

            $table->dropColumn([
                'created_by',
                'closing_manager_id',
                'presales_id',
                'sourcing_manager_id'
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {

            if (!Schema::hasColumn('projects', 'developer_id')) {
                $table->unsignedBigInteger('developer_id')->after('name');
            }

            if (!Schema::hasColumn('projects', 'base_brokerage_percent')) {
                $table->decimal('base_brokerage_percent', 5, 2)->default(0.00);
            }

            if (!Schema::hasColumn('projects', 'rera_number')) {
                $table->string('rera_number')->nullable();
            }
    
            // Optional: fix created_by type
            // (only if mismatch exists)
            // $table->unsignedBigInteger('created_by')->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {

            $table->dropForeign(['developer_id']);

            $table->dropColumn([
                'developer_id',
                'base_brokerage_percent',
                'rera_number'
            ]);
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMandateProjectIdToClientEnquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('client_enquiries', function (Blueprint $table) {

            $table->foreignId('mandate_project_id')
                ->nullable()
                ->after('property_type')
                ->constrained('mandate_projects')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('client_enquiries', function (Blueprint $table) {
            $table->dropForeign(['mandate_project_id']);
            $table->dropColumn('mandate_project_id');
        });
    }
}

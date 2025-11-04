<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandateProjectConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mandate_project_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mandate_project_id')->constrained()->onDelete('cascade');
            $table->string('config'); // e.g. 1BHK, 2BHK
            $table->decimal('carpet_area', 8, 2)->nullable(); // e.g. 650.50
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mandate_project_configurations');
    }
}

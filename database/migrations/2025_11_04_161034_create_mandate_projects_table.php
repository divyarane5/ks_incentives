<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mandate_projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->string('brand_name')->nullable();
            $table->string('location')->nullable();
            $table->string('rera_number')->nullable();
            $table->enum('property_type', ['residential', 'commercial','both'])->nullable();
            $table->foreignId('business_unit_id')->nullable()->constrained()->onDelete('set null');
            $table->tinyInteger('status')->default(1);
            $table->unsignedInteger('created_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mandate_projects');
    }
}

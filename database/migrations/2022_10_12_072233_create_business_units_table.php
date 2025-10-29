<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_units', function (Blueprint $table) {
            
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->string('domain')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('theme_color')->default('#1e40af');
            $table->string('secondary_color')->default('#f0f4f8');
            $table->string('background_path')->nullable(); // login background
            $table->string('favicon_path')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
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
        Schema::dropIfExists('business_units');
    }
}

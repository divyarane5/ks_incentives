<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_partners', function (Blueprint $table) {
            $table->id();
            $table->string('firm_name');
            $table->string('owner_name');
            $table->string('contact');
            $table->string('rera_number')->nullable();
            $table->json('operational_locations')->nullable();
            $table->json('office_locations')->nullable();
            $table->string('sourcing_manager')->nullable();
            $table->enum('acquisition_channel', ['telecalling', 'digital', 'reference', 'BTL']);
            $table->enum('property_type', ['commercial', 'residential']);
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
        Schema::dropIfExists('channel_partners');
    }
}

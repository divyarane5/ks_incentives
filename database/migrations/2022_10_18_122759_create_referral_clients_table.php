<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_clients', function (Blueprint $table) {
            $table->id();
            $table->integer('template_id');
            $table->string('client_name');
            $table->string('sales_person');
            $table->string('subject_name');
            $table->string('client_email');
            $table->tinyInteger('email_sent')->default(0);
            $table->tinyInteger('click')->default(0);
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
        Schema::dropIfExists('referral_clients');
    }
}

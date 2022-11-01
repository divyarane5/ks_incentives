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
            $table->tinyInteger('userid');
            $table->string('name');
            $table->string('email');
            $table->string('mobile');
            $table->string('address')->nullable();
            $table->tinyInteger('assistance')->default(0);
            $table->enum('form_type', ['referrals','homeloan','collection','document','property'])->nullable();
            $table->string('loanamount')->nullable();
            $table->string('preferredbank')->nullable();
            $table->string('remarks')->nullable();
            $table->tinyInteger('status')->default(0);
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('client_enquiry_updates', function (Blueprint $table) {
            $table->id();

            // Link to main client enquiry
            $table->unsignedBigInteger('client_enquiry_id');
            $table->foreign('client_enquiry_id')
                ->references('id')
                ->on('client_enquiries')
                ->onDelete('cascade');

            // Closing manager / updated by
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Updatable follow-up fields
            $table->text('feedback')->nullable();
            $table->date('revisit_scheduled')->nullable();
            $table->date('revisit_done')->nullable();
            $table->date('followup_date')->nullable();

            // Client status
            $table->enum('status', [
                'new',
                'followup',
                'revisit',
                'booked',
                'not_interested',
                'closed'
            ])->default('new');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_enquiry_updates');
    }
};

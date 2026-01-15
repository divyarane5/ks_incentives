<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMandateBookingBrokeragesForFilesAndStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mandate_booking_brokerages', function (Blueprint $table) {
            // Add file upload columns
            $table->string('bill_copy')->nullable()->after('status'); 
            $table->string('acceptance_copy')->nullable()->after('bill_copy');


           
        });
         // Update enum status
            DB::statement("ALTER TABLE mandate_booking_brokerages MODIFY COLUMN status ENUM('pending','approved','paid') NOT NULL DEFAULT 'pending'");
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mandate_booking_brokerages', function (Blueprint $table) {
            $table->dropColumn(['bill_copy', 'acceptance_copy', 'eligible_at', 'paid_at']);
            
            // Revert status to old enum if needed
            $table->enum('status', ['pending', 'eligible', 'approved', 'paid'])
                  ->default('pending')
                  ->change();
        });
    }
}

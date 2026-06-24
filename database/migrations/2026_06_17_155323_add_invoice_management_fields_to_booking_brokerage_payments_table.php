<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceManagementFieldsToBookingBrokeragePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_brokerage_payments', function (Blueprint $table) {

            // Invoice Number
            $table->string('invoice_number')->nullable()->after('booking_id');

            // Invoice Type
            $table->enum('invoice_type', [
                'proforma',
                'tax_invoice',
                'credit_note'
            ])->default('tax_invoice');

            // Billing Entity
            $table->unsignedBigInteger('developer_billing_entity_id')->nullable();

            // GST
            $table->decimal('cgst_percent',5,2)->default(0);
            $table->decimal('cgst_amount',15,2)->default(0);

            $table->decimal('sgst_percent',5,2)->default(0);
            $table->decimal('sgst_amount',15,2)->default(0);

            $table->decimal('total_gst_amount',15,2)->default(0);

            // Credit Note
            $table->date('credit_note_date')->nullable();
            $table->text('credit_note_reason')->nullable();

            // Bank Account
            $table->unsignedBigInteger('company_bank_account_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_brokerage_payments', function (Blueprint $table) {
            //
        });
    }
}

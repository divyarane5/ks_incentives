<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            // Receivable Split
            $table->decimal('amount_receivable',15,2)->default(0)->after('final_revenue');
            $table->decimal('tds_amount',15,2)->default(0)->after('amount_receivable');

            // Payment Summary
            $table->decimal('total_invoice_percent',5,2)->default(0)->after('tds_amount');
            $table->decimal('total_invoice_amount',15,2)->default(0)->after('total_invoice_percent');

            $table->decimal('total_received_amount',15,2)->default(0)->after('total_invoice_amount');

            $table->decimal('pending_brokerage_percent',5,2)->default(0)->after('total_received_amount');
            $table->decimal('pending_brokerage_amount',15,2)->default(0)->after('pending_brokerage_percent');

            // Payment Tracking
            $table->enum('payment_status',['pending','partial','completed'])
                  ->default('pending')
                  ->after('pending_brokerage_amount');

            // Followup Tracking
            $table->date('payment_followup_date')
                  ->nullable()
                  ->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            $table->dropColumn([
                'amount_receivable',
                'tds_amount',
                'total_invoice_percent',
                'total_invoice_amount',
                'total_received_amount',
                'pending_brokerage_percent',
                'pending_brokerage_amount',
                'payment_status',
                'payment_followup_date'
            ]);
        });
    }
};
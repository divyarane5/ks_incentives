<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            // Invoice Summary
            $table->decimal('total_gst_amount', 15, 2)
                ->default(0)
                ->after('total_invoice_amount');

            $table->decimal('total_grand_invoice_amount', 15, 2)
                ->default(0)
                ->after('total_gst_amount');

            // Collection Summary
            $table->decimal('total_actual_receipt_amount', 15, 2)
                ->default(0)
                ->after('total_grand_invoice_amount');

            $table->decimal('pending_collection_amount', 15, 2)
                ->default(0)
                ->after('total_received_amount');

            // Remove unused columns
            $table->dropColumn([
                'total_paid_amount',
                'pending_amount'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            $table->decimal('total_paid_amount', 15, 2)
                ->nullable()
                ->default(0)
                ->after('current_effective_amount');

            $table->decimal('pending_amount', 15, 2)
                ->nullable()
                ->default(0)
                ->after('total_paid_amount');

            $table->dropColumn([
                'total_gst_amount',
                'total_grand_invoice_amount',
                'total_actual_receipt_amount',
                'pending_collection_amount'
            ]);
        });
    }
};
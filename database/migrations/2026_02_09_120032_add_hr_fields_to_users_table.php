<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHrFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            // Probation & confirmation
            $table->integer('probation_period_days')->nullable()->after('joining_date');
            $table->enum('employment_status', ['probation', 'confirmed'])
                  ->default('probation')
                  ->after('probation_period_days');

            // Notice period
            $table->integer('notice_period_days')->nullable()->after('leaving_date');

            // Annual CTC
            $table->decimal('annual_ctc', 12, 2)->nullable()->before('current_ctc');

            // Bank enhancements
            $table->string('bank_account_name')->nullable()->after('bank_name');
            $table->string('bank_branch_name')->nullable()->after('bank_account_name');
            $table->enum('bank_account_type', ['salary', 'savings', 'current'])
                  ->nullable()
                  ->after('bank_branch_name');

            // Offer Letter
            $table->boolean('offer_letter_sent')->default(false)->after('additional_comments');
            $table->boolean('offer_letter_acknowledged')->default(false)->after('offer_letter_sent');
            $table->string('offer_letter_file')->nullable()->after('offer_letter_acknowledged');

            // Joining Letter
            $table->boolean('joining_letter_sent')->default(false)->after('offer_letter_file');
            $table->boolean('joining_letter_acknowledged')->default(false)->after('joining_letter_sent');
            $table->string('joining_letter_file')->nullable()->after('joining_letter_acknowledged');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'probation_period_days',
                'employment_status',
                'notice_period_days',
                'annual_ctc',
                'bank_account_name',
                'bank_branch_name',
                'bank_account_type',
                'offer_letter_sent',
                'offer_letter_acknowledged',
                'offer_letter_file',
                'joining_letter_sent',
                'joining_letter_acknowledged',
                'joining_letter_file',
            ]);
        });
    }
}

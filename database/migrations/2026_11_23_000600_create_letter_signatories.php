<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Signatures on HR letters: who signs (name, designation, signature and stamp images, per company),
 * and on each issued letter which signatory was used, or a PDF signed outside (USB token / DSC).
 * New table and new nullable columns only.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('letter_signatories')) {
            Schema::create('letter_signatories', function (Blueprint $table) {
                $table->id();
                $table->string('company', 190)->nullable(); // empty = any company
                $table->string('name', 120);
                $table->string('designation', 120)->nullable();
                $table->string('signature')->nullable(); // image on the public disk
                $table->string('stamp')->nullable();
                $table->boolean('is_default')->default(false);
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }
        Schema::table('issued_letters', function (Blueprint $table) {
            if (! Schema::hasColumn('issued_letters', 'letter_signatory_id')) {
                $table->unsignedBigInteger('letter_signatory_id')->nullable();
            }
            if (! Schema::hasColumn('issued_letters', 'no_signature')) {
                $table->boolean('no_signature')->default(false);
            }
            if (! Schema::hasColumn('issued_letters', 'signed_pdf')) {
                $table->string('signed_pdf')->nullable(); // PDF signed with a DSC token, uploaded back
            }
        });
    }

    public function down(): void
    {
        Schema::table('issued_letters', function (Blueprint $table) {
            foreach (['letter_signatory_id', 'no_signature', 'signed_pdf'] as $c) {
                if (Schema::hasColumn('issued_letters', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
        Schema::dropIfExists('letter_signatories');
    }
};

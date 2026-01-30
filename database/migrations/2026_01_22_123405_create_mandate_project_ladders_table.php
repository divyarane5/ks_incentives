<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandateProjectLaddersTable extends Migration
{
    public function up()
    {
        Schema::create('mandate_project_ladders', function (Blueprint $table) {

            $table->id();

            // Parent relation (ONE project → MANY ladders)
            $table->foreignId('mandate_project_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Timeline slab
            $table->date('timeline_from');
            $table->date('timeline_to');

            // Business logic fields
            $table->unsignedInteger('no_of_units');
            $table->decimal('payout_percentage', 5, 2); // e.g. 2.65%

            // Common system fields
            $table->tinyInteger('status')->default(1);
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate ladder slabs for same project
            $table->unique([
                'mandate_project_id',
                'timeline_from',
                'timeline_to'
            ]);

        });
    }

    public function down()
    {
        Schema::dropIfExists('mandate_project_ladders');
    }
}

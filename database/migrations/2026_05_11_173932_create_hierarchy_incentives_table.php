<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHierarchyIncentivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hierarchy_incentives', function (Blueprint $table) {

            $table->id();

            // Person who gets hierarchy incentive
            $table->unsignedBigInteger('user_id');

            // Source FOS user from whom incentive came
            $table->unsignedBigInteger('source_user_id');

            // TL / SR_TL / CH
            $table->string('role');

            // Percentage earned
            $table->decimal('percent', 5, 2)->default(0);

            // Incentive amount
            $table->decimal('amount', 15, 2)->default(0);

            // Financial year
            $table->string('fy');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->foreign('source_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hierarchy_incentives');
    }
}

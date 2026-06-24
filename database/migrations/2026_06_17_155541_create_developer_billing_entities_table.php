    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateDeveloperBillingEntitiesTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('developer_billing_entities', function (Blueprint $table) {

                $table->id();

                $table->unsignedBigInteger('developer_id');

                $table->string('entity_name');

                $table->string('gstin')->nullable();

                $table->boolean('status')->default(1);

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
            Schema::dropIfExists('developer_billing_entities');
        }
    }

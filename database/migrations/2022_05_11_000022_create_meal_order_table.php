        <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meal_order', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // if the order has been deleted all the related meals (relation) will be deleted not the meal it self
            $table->foreignId('order_id')->nullable(false)->constrained()->cascadeOnDelete();
            // if the meal is deleted (softdeleted) the the orders that had this meal should not lost it (get with trash in the model)
            $table->foreignId('meal_id')->nullable(false)->constrained();
            $table->unsignedTinyInteger('quantity')->nullable(false);
            $table->string('notes',500)->nullable();
            $table->unsignedTinyInteger('meal_rate')->nullable();
            $table->string('meal_rate_notes',500)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meals_orders');
    }
};

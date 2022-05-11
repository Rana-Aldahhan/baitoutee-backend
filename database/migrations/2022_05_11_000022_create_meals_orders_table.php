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
        Schema::create('meals_orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('order_id')->nullable(false)->constrained();
            $table->foreignId('meal_id')->nullable(false)->constrained();
            $table->unsignedTinyInteger('meal_quantity')->nullable(false);
            $table->string('notes',500);
            $table->unsignedTinyInteger('meal_rate');
            $table->string('meal_rate_notes',500);

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

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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // if the chef has been deleted the subscription should not be deleted (get with trash in the model)
            $table->foreignId('chef_id')->nullable(false)->constrained(); // ->references('id)->on('locations');
            $table->string('name', 50)->nullable(false);
            $table->unsignedTinyInteger('days_number')->nullable(false);
            $table->time('meal_delivery_time')->nullable(false);
            $table->boolean('is_available')->nullable(false)->default(false);
            $table->date('starts_at')->nullable(false);
            $table->unsignedSmallInteger('max_subscribers');
            $table->unsignedInteger('meals_cost')->nullable(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
};

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
        Schema::create('meal_subscription', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('day_number')->default(1);
            $table->foreignId('meal_id')->nullable(false)->constrained()->onDelete('cascade');;
            $table->foreignId('subscription_id')->nullable(false)->constrained()->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meals_subscriptions');
    }
};

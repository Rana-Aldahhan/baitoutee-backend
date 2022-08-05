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
        Schema::create('subscription_user', function (Blueprint $table) {
            $table->id();
            // if the user has been deleted his subscription should be deleted to show it for the chef
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            // if the subscription has been deleted the user subscription should not be deleted (get with trash in the model)
            $table->foreignId('subscription_id')->references('id')->on('subscriptions');
            $table->string('notes');
            $table->boolean('paid')->default(false);
            $table->float('total_cost', 8, 2);
            $table->float('delivery_cost_per_day',6,2);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_subscription');
    }
};

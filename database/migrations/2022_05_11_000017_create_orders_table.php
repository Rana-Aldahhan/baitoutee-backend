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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('chef_id')->references('id')->on('chefs');
            $table->foreignId('delivery_id')->nullable()->references('id')->on('deliveries');
            $table->foreignId('subscription_id')->nullable()->references('id')->on('subscriptions');
            $table->timestamp('selected_delivery_time');
            $table->string('notes')->nullable();
            $table->enum('status',['pending','approved','notApproved','prepared','failedAssigning','picked','delivered','notDelivered','canceled'])->default('pending');
            $table->float('total_cost');
            $table->float('meals_cost');
            $table->float('profit');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('prepared_at')->nullable();
            $table->boolean('paid_to_chef')->default(false);
            $table->boolean('paid_to_accountant')->default(false);
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
        Schema::dropIfExists('orders');
    }
};

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
            $table->foreignId('subscriptions_id')->nullable()->references('id')->on('subscriptions');
            $table->time('selected_delivery_time');
            $table->string('notes');
            $table->enum('status',['pending','approved','not approved','prepared','picked','delivered','not delivered','canceled'])->default('pending');
            $table->float('total_cost', 8, 2);
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
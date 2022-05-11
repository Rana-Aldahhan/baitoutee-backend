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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('deliveryman_id')->nullable(false)->constrained('deliverymen');
            $table->unsignedInteger('distance');
            $table->unsignedInteger('cost');
            $table->timestamp('picked_at');
            $table->timestamp('delivered_at');
            $table->boolean('paid_to_deliveryman')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
};

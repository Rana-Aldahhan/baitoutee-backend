<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new  class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_change_requests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('meal_id')->nullable(false)->constrained();
            $table->unsignedInteger('new_price')->nullable(false);
            $table->string('reason', 250)->nullable(false);
            $table->boolean('approved')->nullable()->default(null);;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_change_requests');
    }
};

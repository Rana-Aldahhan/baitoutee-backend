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
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('chef_id')->nullable(false)->constrained(); // ->references('id)->on('locations');
            $table->foreignId('category_id')->nullable(false)->constrained(); // ->references('id)->on('locations');
            $table->string('image')->nullable(false)->default('');
            $table->string('name', 50)->nullable(false);
            $table->unsignedInteger('price')->nullable(false);
            $table->unsignedTinyInteger('max_meals_per_day')->nullable(false);
            $table->boolean('is_available')->nullable(false)->default(false);
            $table->unsignedTinyInteger('expected_preparation_time')->nullable(false); // in minutes
            $table->unsignedTinyInteger('discount_percentage')->nullable(true);
            $table->mediumText('ingredients')->nullable(false);
            $table->float('rating')->nullable(true);
            $table->unsignedInteger('rates_count')->default(0);
            $table->softDeletes();
            $table->boolean('approved')->nullable()->default(null);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meals');
    }
};

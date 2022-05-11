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
        Schema::create('chefs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('chef_join_request_id')->nullable(false)->constrained(); // ->references('id)->on('locations');
            $table->string('phone_number', 10)->nullable(false);
            $table->string('name', 50)->nullable(false);
            $table->string('email',50)->nullable(false)->unique();
            $table->date('birth_date')->nullable(false);
            $table->enum('gender', ['m', 'f'])->nullable(false);
            $table->foreignId('location_id')->nullable(false)->constrained(); // ->references('id)->on('locations');
            $table->time('delivery_starts_at')->nullable(false);
            $table->time('delivery_ends_at')->nullable(false);
            $table->unsignedTinyInteger('max_meals_per_day')->nullable(false);
            $table->boolean('is_available')->nullable(false)->default(false);
            $table->integer('balance')->nullable(false)->default(0);
            $table->string('profile_picture', 50)->nullable(false)->default('default_profile_pic');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('certificate')->nullable(true);


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chefs');
    }
};

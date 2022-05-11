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
        Schema::create('user_join_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->references('id')->on('locations');
            $table->foreignId('user_id')->nullable()->references('id')->on('users');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_number')->unique();
            $table->date('birth_date');
            $table->enum('gender',['m','f']);
            $table->string('national_id');
            $table->string('campus_card_id');
            $table->int('campus_unit_number');
            $table->date('campus_card_expiry_date');
            $table->string('study_specialty');
            $table->smallInteger('study_year');
            $table->boolean('approved')->nullable();
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
        Schema::dropIfExists('user_join_requests');
    }
};

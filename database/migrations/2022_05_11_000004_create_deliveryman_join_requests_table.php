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
        Schema::create('deliveryman_join_requests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('phone_number', 10)->nullable(false);
            $table->string('name', 50)->nullable(false);
            $table->string('email',50)->nullable(false)->unique();
            $table->date('birth_date')->nullable(false);
            $table->enum('gender', ['m', 'f'])->nullable(false);
            $table->enum('transportation_type', ['bicycle','electricBicycle', 'motorcycle','car'])->nullable(false);
            $table->string('work_days',50)->nullable(false);
            $table->time('work_hours_from',)->nullable(false);
            $table->time('work_hours_to')->nullable(false);
            $table->boolean('approved')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliveryman_join_requests');
    }
};

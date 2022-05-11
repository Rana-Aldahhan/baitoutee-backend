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
        Schema::create('deliverymen', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('deliveryman_join_request_id')->nullable(false)->constrained(); // ->references('id)->on('locations');
            $table->string('phone_number', 10)->nullable(false);
            $table->string('name', 50)->nullable(false);
            $table->string('email',50)->nullable(false)->unique();
            $table->date('birth_date')->nullable(false);
            $table->enum('gender', ['m', 'f'])->nullable(false);
            $table->enum('transportation_type', ['دراجة هوائية','دراجة كهربائية', 'دراجة نارية','سيارة'])->nullable(false);
            $table->string('work_days',50)->nullable(false);
            $table->time('work_hours_from',)->nullable(false);
            $table->time('work_hours_to')->nullable(false);
            $table->boolean('is_available')->nullable(false)->default(false);
            $table->integer('balance')->nullable(false)->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->float('current_longitude', 17, 15);
            $table->float('current_latitude', 17, 15);
            $table->timestamp('deleted_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliverymen');
    }
};

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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('dorm_id')->unsigned()->comment('Foreign key from table dorms');
            $table->string('name', 150);
            $table->string('type_of_room', 30);
            $table->string('is_aircon', 10);
            $table->string('furnished_type', 20);
            $table->string('image', 100);
            $table->string('fee', 100);
            $table->string('deposit', 100);
            $table->string('advance', 100);
            $table->boolean('is_available')->default(true);

            $table->foreign('dorm_id')->references('id')->on('dorms');
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
        Schema::dropIfExists('rooms');
    }
};

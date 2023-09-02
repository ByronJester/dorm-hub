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
            $table->longText('name');
            $table->longText('description');
            $table->string('image');
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

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
        Schema::create('dorms', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->comment('Foreign key from table users');
            $table->longText('map_address');
            $table->longText('detailed_address');
            $table->string('lat', 100);
            $table->string('long', 100);
            $table->longText('property_name');
            $table->longText('description');
            $table->string('floors_total', 100);
            $table->string('rooms_total', 100);
            $table->string('dorm_image', 100);
            $table->string('business_permit_image', 100);
            $table->string('landmark', 100);
            $table->string('note', 255);
            $table->string('reason', 255)->nullable();
            $table->string('reservation', 100);
            $table->string('status', 20)->default('pending');
            $table->string('ratings', 20)->nullable();
            $table->longText('terms')->nullable();


            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('dorms');
    }
};

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
        Schema::create('dorm_ratings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('dorm_id')->unsigned()->comment('Foreign key from table dorms');
            $table->bigInteger('tenant_id')->unsigned()->comment('Foreign key from table users');
            $table->string('rate');
            $table->longText('comment')->nullable();

            $table->foreign('dorm_id')->references('id')->on('dorms');
            $table->foreign('tenant_id')->references('id')->on('users');
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
        Schema::dropIfExists('dorm_ratings');
    }
};

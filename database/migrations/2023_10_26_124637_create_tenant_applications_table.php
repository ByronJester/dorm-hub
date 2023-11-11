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
        Schema::create('tenant_applications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('owner_id')->unsigned()->comment('Foreign key from table users');
            $table->bigInteger('tenant_id')->unsigned()->comment('Foreign key from table users');
            $table->bigInteger('dorm_id')->unsigned()->comment('Foreign key from table dorms');
            $table->bigInteger('room_id')->unsigned()->comment('Foreign key from table rooms');
            $table->string('status');
            $table->date('move_in')->nullable();
            $table->boolean('is_approved')->default(false);

            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('tenant_id')->references('id')->on('users');
            $table->foreign('dorm_id')->references('id')->on('dorms');
            $table->foreign('room_id')->references('id')->on('rooms');
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
        Schema::dropIfExists('tenant_applications');
    }
};

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
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('owner_id')->nullable()->unsigned()->comment('Foreign key from table users');
            $table->bigInteger('tenant_id')->nullable()->unsigned()->comment('Foreign key from table users');
            $table->string('name', 150)->nullable();

            $table->foreign('owner_id')->references('id')->on('users');
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
        Schema::dropIfExists('threads');
    }
};

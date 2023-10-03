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
        Schema::create('thread_members', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable()->unsigned()->comment('Foreign key from table users');
            $table->bigInteger('thread_id')->nullable()->unsigned()->comment('Foreign key from table threads');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('thread_id')->references('id')->on('threads');
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
        Schema::dropIfExists('thread_members');
    }
};

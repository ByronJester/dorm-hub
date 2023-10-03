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
        Schema::create('thread_messages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('thread_id')->unsigned()->comment('Foreign key from table threads');
            $table->bigInteger('thread_member_id')->nullable()->unsigned()->comment('Foreign key from table thread_members');
            $table->bigInteger('user_id')->unsigned()->comment('Foreign key from table users');
            $table->longText('message');
            $table->string('files')->nullable();

            $table->foreign('thread_id')->references('id')->on('threads');
            $table->foreign('thread_member_id')->references('id')->on('thread_members');
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
        Schema::dropIfExists('thread_messages');
    }
};

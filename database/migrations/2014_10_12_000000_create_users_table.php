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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('last_name', 50);
            $table->string('phone_number', 20);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('id_picture', 100)->nullable();
            $table->string('image', 100)->nullable();
            $table->longText('bio')->nullable();
            $table->string('user_type', 10)->default('tenant');
            $table->string('sk', 100)->nullable();
            $table->string('pk', 100)->nullable();
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_active')->default(true);

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};

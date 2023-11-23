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
            $table->string('username')->unique();
            $table->string('password');
            $table->string('id_picture', 100)->nullable();
            $table->string('selfie_id_picture', 100)->nullable();
            $table->string('image', 100)->nullable();
            $table->longText('bio')->nullable();
            $table->string('user_type', 10)->default('tenant');
            $table->string('sk', 100)->nullable();
            $table->longText('reason')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('subscription', 255)->nullable();
            $table->string('pk', 100)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('account_name', 100)->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('status', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('first_logged_in')->default(true);

            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login')->nullable();
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

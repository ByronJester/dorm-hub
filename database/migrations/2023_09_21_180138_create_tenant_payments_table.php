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
        Schema::create('tenant_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tenant_room_id')->unsigned()->comment('Foreign key from table tenant_rooms');
            $table->bigInteger('user_id')->unsigned()->comment('Foreign key from table users');
            $table->string('amount_to_pay', 100);
            $table->string('amount_paid', 100)->nullable();
            $table->string('pending_payment', 100)->nullable();
            $table->string('partial', 100)->nullable();
            $table->string('mode_of_payment', 20)->nullable();
            $table->string('receipt', 100)->nullable();
            $table->string('partial_receipt', 100)->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamp('date')->nullable();

            $table->foreign('tenant_room_id')->references('id')->on('tenant_rooms');
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
        Schema::dropIfExists('tenant_payments');
    }
};

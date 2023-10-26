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
            $table->bigInteger('tenant_id')->unsigned()->comment('Foreign key from table users');
            $table->bigInteger('tenant_billing_id')->unsigned()->comment('Foreign key from table tenant_billings');
            $table->bigInteger('dorm_id')->unsigned()->comment('Foreign key from table dorms');
            $table->bigInteger('room_id')->unsigned()->comment('Foreign key from table rooms');
            $table->float('amount')->default(0);
            $table->string('category', 100)->nullable();
            $table->string('payment_method', 100)->nullable();
            $table->string('proof_of_payment', 100)->nullable();
            $table->string('status', 30)->default('pending');
            $table->date('date')->nullable();
            $table->longText('description')->nullable();

            $table->foreign('tenant_id')->references('id')->on('users');
            $table->foreign('tenant_billing_id')->references('id')->on('tenant_billings');
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
        Schema::dropIfExists('tenant_payments');
    }
};

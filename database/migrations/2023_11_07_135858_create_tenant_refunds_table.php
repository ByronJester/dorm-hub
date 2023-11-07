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
        Schema::create('tenant_refunds', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tenant_application_id')->unsigned()->comment('Foreign key from table tenant_applications');
            $table->bigInteger('tenant_payment_id')->unsigned()->nullable()->comment('Foreign key from table tenant_payments');
            $table->bigInteger('tenant_id')->unsigned()->comment('Foreign key from table users');
            $table->float('amount')->default(0);
            $table->string('payment_method', 100)->nullable();
            $table->string('wallet_name', 100)->nullable();
            $table->string('account_name', 100)->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('status', 50)->default('pending');

            $table->foreign('tenant_application_id')->references('id')->on('tenant_applications');
            $table->foreign('tenant_payment_id')->references('id')->on('tenant_payments');
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
        Schema::dropIfExists('tenant_refunds');
    }
};

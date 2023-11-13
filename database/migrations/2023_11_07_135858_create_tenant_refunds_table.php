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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_payment_id')->unsigned()->nullable()->comment('Foreign key from table user_payments');
            $table->float('amount')->default(0);
            $table->string('payment_method', 100)->nullable();
            $table->string('wallet_name', 100)->nullable();
            $table->string('account_name', 100)->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('status', 50)->default('pending');

            $table->foreign('user_payment_id')->references('id')->on('user_payments');
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
        Schema::dropIfExists('refunds');
    }
};

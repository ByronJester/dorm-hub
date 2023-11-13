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
        Schema::create('user_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tenant_id')->nullable()->comment('Foreign key from table tenants');
            $table->bigInteger('reservation_id')->nullable()->comment('Foreign key from table reservations');
            $table->bigInteger('user_id')->nullable()->comment('Foreign key from table users');
            $table->bigInteger('billing_id');
            $table->string('payment_method', 100)->nullable();
            $table->string('proof_of_payment', 100)->nullable();
            $table->string('status', 30)->default('pending');
            $table->longText('description')->nullable();
            $table->date('date')->nullable();

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
        Schema::dropIfExists('user_payments');
    }
};

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
        Schema::create('tenant_billings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tenant')->unsigned()->comment('Foreign key from table users');
            $table->bigInteger('tenant_id')->unsigned()->comment('Foreign key from table tenants');
            $table->float('amount')->default(0);
            $table->string('subject')->nullable();
            $table->longText('description')->nullable();
            $table->date('date')->nullable();
            $table->boolean('is_paid')->default(false);

            $table->foreign('tenant')->references('id')->on('users');
            $table->foreign('tenant_id')->references('id')->on('tenants');
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
        Schema::dropIfExists('tenant_billings');
    }
};

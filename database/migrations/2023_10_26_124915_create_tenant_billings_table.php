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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tenant_id')->nullable()->comment('Foreign key from table tenants');
            $table->bigInteger('reservation_id')->nullable()->comment('Foreign key from table reservations');
            $table->bigInteger('user_id')->nullable()->comment('Foreign key from table users');
            $table->float('amount')->default(0);
            $table->string('subject')->nullable();
            $table->longText('description')->nullable();
            $table->date('date')->nullable();
            $table->boolean('is_paid')->default(false);

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
        Schema::dropIfExists('billings');
    }
};

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
            $table->bigInteger('f_id');
            $table->bigInteger('profile_id');
            $table->bigInteger('user_id');
            $table->float('amount')->default(0);
            $table->string('subject')->nullable();
            $table->longText('description')->nullable();
            $table->string('type');
            $table->string('invoice_number')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->date('payment_date')->nullable();
            $table->date('for_the_month')->nullable();
            $table->boolean('is_overdue')->default(false);
            $table->boolean('is_active')->default(true);

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

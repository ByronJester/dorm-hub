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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('owner')->unsigned()->comment('Foreign key from table users');
            $table->bigInteger('tenant')->unsigned()->comment('Foreign key from table users');
            $table->bigInteger('dorm_id')->unsigned()->comment('Foreign key from table dorms');
            $table->bigInteger('room_id')->unsigned()->comment('Foreign key from table rooms');
            $table->bigInteger('profile_id');
            $table->string('status');
            $table->date('move_in')->nullable();
            $table->date('move_out')->nullable();
            $table->longText('reason')->nullable();
            $table->longText('reason_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('pay_on')->nullable();
            $table->boolean('is_delinquent')->default(false);
            $table->boolean('auto_bill')->default(false);
            $table->date('auto_bill_date')->nullable();
            $table->boolean('is_d')->default(false);
            $table->date('promise_date')->nullable();

            $table->foreign('owner')->references('id')->on('users');
            $table->foreign('tenant')->references('id')->on('users');
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
        Schema::dropIfExists('tenants');
    }
};

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
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('dorm_id')->unsigned()->comment('Foreign key from table dorms');
            $table->string('short_term', 20);
            $table->string('mix_gender', 20);
            $table->string('curfew', 20);
            $table->string('curfew_hours', 100)->nullable();
            $table->string('minimum_stay', 100)->nullable();
            $table->longText('rules')->nullable();

            $table->foreign('dorm_id')->references('id')->on('dorms');
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
        Schema::dropIfExists('rules');
    }
};

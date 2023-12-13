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
        Schema::create('user_income_informations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('profile_id')->unsigned()->comment('Foreign key from table profiles');
            $table->string('source_of_income', 100);
            $table->string('monthly_income', 100);
            $table->string('monthly_expenses', 100)->nullable();
            $table->string('proof', 100);

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
        Schema::dropIfExists('user_income_informations');
    }
};

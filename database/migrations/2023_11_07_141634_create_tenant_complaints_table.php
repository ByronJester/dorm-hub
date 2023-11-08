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
        Schema::create('tenant_complaints', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tenant_application_id')->unsigned()->comment('Foreign key from table tenant_applications');
            $table->string('subject', 100);
            $table->longText('complain')->nullable();
            $table->string('status', 50)->default("pending");

            $table->foreign('tenant_application_id')->references('id')->on('tenant_applications');
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
        Schema::dropIfExists('tenant_complaints');
    }
};

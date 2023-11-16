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
        Schema::create('backup_and_restore', function (Blueprint $table) {
            $table->id();
            $table->string('user_id'); // Assuming you want to record the user who performed the operation
            $table->enum('operation_type', ['backup', 'restore']);
            $table->timestamp('operation_date')->default(now());
            // Add other columns as needed
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
        Schema::dropIfExists('backup_and_restore');
    }
};

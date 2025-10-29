<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('soldiers_cmds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('soldier_id');
            $table->unsignedBigInteger('cmd_id');

            $table->string('remarks', 400)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Foreign key constraints
            $table->foreign('soldier_id')
                ->references('id')
                ->on('soldiers')
                ->onDelete('cascade');

            $table->foreign('cmd_id')
                ->references('id')
                ->on('cmds')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soldiers_cmds');
    }
};

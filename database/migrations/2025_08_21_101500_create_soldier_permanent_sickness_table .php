<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soldier_permanent_sickness', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('soldier_id')->constrained('soldiers')->cascadeOnDelete();
            $table->unsignedBigInteger('permanent_sickness_id');
            $table->text('remarks')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('permanent_sickness_id')->references('id')->on('permanent_sickness')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soldier_permanent_sickness');
    }
};

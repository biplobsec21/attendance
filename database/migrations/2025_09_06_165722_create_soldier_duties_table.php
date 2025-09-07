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
        Schema::create('soldier_duties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soldier_id')->constrained('soldiers')->onDelete('cascade');
            $table->foreignId('duty_id')->constrained('duties')->onDelete('cascade');

            $table->date('assigned_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('status', ['assigned', 'cancelled', 'transferred', 'completed'])->default('assigned');
            $table->string('remarks')->nullable();
            $table->timestamps();

            // Ensure no duplicate assignment of same duty to same soldier on same day
            $table->unique(['soldier_id', 'duty_id', 'assigned_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soldier_duties');
    }
};

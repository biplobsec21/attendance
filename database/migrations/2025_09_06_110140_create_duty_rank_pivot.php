<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duty_rank', function (Blueprint $table) {
            $table->id();

            // Foreign keys referencing the correct table names
            $table->foreignId('duty_id')->constrained('duties')->onDelete('cascade');
            $table->foreignId('rank_id')->constrained('ranks')->onDelete('cascade');

            // Extra fields for this assignment
            $table->enum('duty_type', ['fixed', 'roster', 'regular'])->default('regular');
            $table->string('remarks')->nullable();
            $table->tinyInteger('priority')->default(1); // 1=highest, larger=lower
            $table->integer('rotation_days')->nullable(); // e.g., 3 days rotation
            $table->foreignId('fixed_soldier_id')->nullable()->constrained('soldiers'); // applicable when duty is fixed

            $table->integer('manpower');
            $table->timestamps();

            // Ensure one duty-rank combination is unique
            $table->unique(['duty_id', 'rank_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duty_rank');
    }
};

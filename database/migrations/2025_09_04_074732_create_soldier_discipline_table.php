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
        Schema::create('soldiers_discipline', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('soldier_id')->constrained('soldiers')->cascadeOnDelete();
            $table->text('discipline_type')->nullable();
            $table->text('discipline_name')->nullable();
            $table->text('remarks')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soldiers_discipline');
    }
};

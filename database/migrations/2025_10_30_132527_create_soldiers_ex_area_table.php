<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soldier_ex_areas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('soldier_id')->constrained('soldiers')->cascadeOnDelete();
            $table->foreignId('ex_area_id')->constrained('ex_areas')->cascadeOnDelete(); // Using foreignId for consistency

            $table->text('result')->nullable();
            $table->string('course_status')->nullable();
            $table->date('completion_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // No need for separate foreign key constraint when using foreignId()->constrained()
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soldier_ex_areas');
    }
};

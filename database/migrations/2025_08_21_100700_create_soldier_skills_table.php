<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soldier_skills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('soldier_id');
            $table->unsignedInteger('skill_id');
            $table->enum('proficiency_level', ['Beginner', 'Intermediate', 'Advanced', 'Expert'])->default('Beginner');
            $table->string('remarks', 400)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // $table->primary(['soldier_id', 'skill_id']);
            $table->foreign('soldier_id')->references('id')->on('soldiers')->cascadeOnDelete();
            $table->foreign('skill_id')->references('id')->on('skills')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soldier_skills');
    }
};

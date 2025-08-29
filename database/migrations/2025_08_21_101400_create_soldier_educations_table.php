<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soldier_educations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('soldier_id')->constrained('soldiers')->cascadeOnDelete();
            $table->unsignedInteger('education_id');
            $table->text('remarks')->nullable();
            $table->text('passing_year')->nullable();
            $table->text('result')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('education_id')->references('id')->on('educations')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soldier_educations');
    }
};

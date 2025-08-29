<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->string('name', 100);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->boolean('status')->default(true); // true = active, false = inactive

            $table->unique(['category_id', 'name']);
            $table->foreign('category_id')->references('id')->on('skill_categories');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};

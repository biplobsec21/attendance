<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_rank_manpower', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rank_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('manpower_number')->default(0);
            $table->timestamps();
            $table->unique(['company_id', 'rank_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_rank_manpower');
    }
};

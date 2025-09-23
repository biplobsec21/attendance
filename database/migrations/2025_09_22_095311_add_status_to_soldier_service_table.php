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
        Schema::table('soldier_services', function (Blueprint $table) {
            $table->enum('status', ['scheduled', 'active', 'completed'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soldier_services', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

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
        // Add the 'status' column to the 'soldier_cadres' table
        Schema::table('soldier_cadres', function (Blueprint $table) {
            $table->enum('status', ['scheduled', 'active', 'completed'])->default('active');
        });

        // Add the 'status' column to the 'soldier_courses' table
        Schema::table('soldier_courses', function (Blueprint $table) {
            $table->enum('status', ['scheduled', 'active', 'completed'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the 'status' column from the 'soldier_cadres' table
        Schema::table('soldier_cadres', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Drop the 'status' column from the 'soldier_courses' table
        Schema::table('soldier_courses', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

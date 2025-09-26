<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add columns back to soldier_courses table
        Schema::table('soldier_courses', function (Blueprint $table) {
            $table->date('completion_date')->nullable()->after('end_date');
            $table->string('course_status')->nullable()->after('completion_date');
        });

        // Add columns back to soldier_cadres table
        Schema::table('soldier_cadres', function (Blueprint $table) {
            $table->date('completion_date')->nullable()->after('end_date');
            $table->string('course_status')->nullable()->after('completion_date');
        });
    }

    public function down()
    {
        // Remove columns from soldier_courses table
        Schema::table('soldier_courses', function (Blueprint $table) {
            $table->dropColumn(['completion_date', 'course_status']);
        });

        // Remove columns from soldier_cadres table
        Schema::table('soldier_cadres', function (Blueprint $table) {
            $table->dropColumn(['completion_date', 'course_status']);
        });
    }
};

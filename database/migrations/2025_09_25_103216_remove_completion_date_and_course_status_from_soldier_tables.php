<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('soldier_courses', function (Blueprint $table) {
            $table->dropColumn(['completion_date', 'course_status']);
        });

        Schema::table('soldier_cadres', function (Blueprint $table) {
            $table->dropColumn(['completion_date', 'course_status']);
        });
    }

    public function down()
    {
        Schema::table('soldier_courses', function (Blueprint $table) {
            $table->date('completion_date')->nullable();
            $table->string('course_status')->nullable();
        });

        Schema::table('soldier_cadres', function (Blueprint $table) {
            $table->date('completion_date')->nullable();
            $table->string('course_status')->nullable();
        });
    }
};

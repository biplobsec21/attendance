<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecommendationIdToAssignments extends Migration
{
    public function up()
    {
        Schema::table('soldier_courses', function (Blueprint $table) {
            $table->foreignId('recommendation_id')->nullable()->constrained('instruction_recomendations')->nullOnDelete();
        });

        Schema::table('soldier_cadres', function (Blueprint $table) {
            $table->foreignId('recommendation_id')->nullable()->constrained('instruction_recomendations')->nullOnDelete();
        });

        Schema::table('soldier_ex_areas', function (Blueprint $table) {
            $table->foreignId('recommendation_id')->nullable()->constrained('instruction_recomendations')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('soldier_courses', function (Blueprint $table) {
            $table->dropForeign(['recommendation_id']);
            $table->dropColumn('recommendation_id');
        });

        Schema::table('soldier_cadres', function (Blueprint $table) {
            $table->dropForeign(['recommendation_id']);
            $table->dropColumn('recommendation_id');
        });

        Schema::table('soldier_ex_areas', function (Blueprint $table) {
            $table->dropForeign(['recommendation_id']);
            $table->dropColumn('recommendation_id');
        });
    }
}

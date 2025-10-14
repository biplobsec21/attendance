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
        Schema::table('duties', function (Blueprint $table) {
            $table->renameColumn('excused_next_day_pt', 'excused_next_session_pt');
            $table->renameColumn('excused_next_day_games', 'excused_next_session_games');
            $table->renameColumn('excused_next_day_roll_call', 'excused_next_session_roll_call');
            $table->renameColumn('excused_next_day_parade', 'excused_next_session_parade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('duties', function (Blueprint $table) {
            $table->renameColumn('excused_next_session_pt', 'excused_next_day_pt');
            $table->renameColumn('excused_next_session_games', 'excused_next_day_games');
            $table->renameColumn('excused_next_session_roll_call', 'excused_next_day_roll_call');
            $table->renameColumn('excused_next_session_parade', 'excused_next_day_parade');
        });
    }
};

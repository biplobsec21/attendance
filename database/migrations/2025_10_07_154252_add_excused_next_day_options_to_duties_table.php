<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('duties', function (Blueprint $table) {
            $table->boolean('excused_next_day_pt')->default(false)->after('status');
            $table->boolean('excused_next_day_games')->default(false)->after('excused_next_day_pt');
            $table->boolean('excused_next_day_roll_call')->default(false)->after('excused_next_day_games');
            $table->boolean('excused_next_day_parade')->default(false)->after('excused_next_day_roll_call');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('duties', function (Blueprint $table) {
            $table->dropColumn([
                'excused_next_day_pt',
                'excused_next_day_games',
                'excused_next_day_roll_call',
                'excused_next_day_parade'
            ]);
        });
    }
};

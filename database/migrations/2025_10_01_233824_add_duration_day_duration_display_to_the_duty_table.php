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
        // Add duration field
        Schema::table('duties', function (Blueprint $table) {
            $table->integer('duration_days')->default(1)->after('end_time');
            //     $table->string('duration_display')->virtualAs(
            //         "CASE
            //     WHEN duration_days = 1 THEN CONCAT(start_time, ' - ', end_time)
            //     WHEN duration_days > 1 THEN CONCAT(start_time, ' to ', end_time, ' (', duration_days, ' days)')
            // END"
            //     );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('duties', function (Blueprint $table) {
            //
            $table->dropColumn('duration_days');
            // $table->dropColumn('duration_display');
        });
    }
};

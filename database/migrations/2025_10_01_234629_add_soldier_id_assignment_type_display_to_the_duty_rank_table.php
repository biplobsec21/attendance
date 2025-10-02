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
        // Create migration: php artisan make:migration add_fixed_duty_to_duty_ranks
        Schema::table('duty_rank', function (Blueprint $table) {
            $table->foreignId('soldier_id')->nullable()->after('rank_id');
            $table->string('assignment_type')->default('roster')->after('soldier_id'); // 'roster' or 'fixed'
            $table->integer('duration_days')->default(1)->after('soldier_id');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            // Add foreign key constraint
            $table->foreign('soldier_id')->references('id')->on('soldiers')->onDelete('cascade');
        });

        // Add index for better performance
        Schema::table('duty_rank', function (Blueprint $table) {
            $table->index(['assignment_type', 'soldier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('duty_rank', function (Blueprint $table) {
            //
            $table->dropForeign(['soldier_id']);
            $table->dropColumn('soldier_id');
            $table->dropColumn('assignment_type');
            $table->dropColumn('duration_days');
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
            $table->dropIndex(['assignment_type', 'soldier_id']);
        });
    }
};

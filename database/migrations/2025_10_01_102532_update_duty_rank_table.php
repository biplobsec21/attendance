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
        Schema::table('duty_rank', function (Blueprint $table) {
            //
            $table->string('group_id')->nullable()->after('duty_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('duty_rank', function (Blueprint $table) {
            //
            $table->dropColumn('group_id');
        });
    }
};

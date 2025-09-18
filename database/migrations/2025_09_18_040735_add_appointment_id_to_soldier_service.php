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
            //
            $table->unsignedBigInteger('appointment_id')->after('soldier_id');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soldier_services', function (Blueprint $table) {
            //
            $table->dropColumn('appointment_id');
            $table->dropForeign('appointment_id');
        });
    }
};

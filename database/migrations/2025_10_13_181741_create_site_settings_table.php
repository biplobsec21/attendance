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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->nullable();
            $table->time('pt_time')->nullable();
            $table->time('games_time')->nullable();
            $table->time('parade_time')->nullable();
            $table->time('roll_call_time')->nullable();
            $table->timestamps();
        });

        // Insert a default record
        DB::table('site_settings')->insert([
            'site_name' => 'Military Records',
            'pt_time' => '06:00:00',
            'games_time' => '16:00:00',
            'parade_time' => '08:00:00',
            'roll_call_time' => '07:30:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};

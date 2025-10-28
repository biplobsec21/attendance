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
        Schema::table('soldiers', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('medical_completed');
            $table->string('family_mobile_1', 20)->nullable()->after('notes');
            $table->string('family_mobile_2', 20)->nullable()->after('family_mobile_1');
            $table->enum('living_type', ['cantonment', 'rental', 'bachelor_mess'])->nullable()->after('family_mobile_2');
            $table->string('living_address', 255)->nullable()->after('living_type');
            $table->unsignedTinyInteger('no_of_brothers')->default(0)->after('living_address');
            $table->unsignedTinyInteger('no_of_sisters')->default(0)->after('no_of_brothers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soldiers', function (Blueprint $table) {
            $table->dropColumn([
                'notes',
                'family_mobile_1',
                'family_mobile_2',
                'living_type',
                'living_address',
                'no_of_brothers',
                'no_of_sisters'
            ]);
        });
    }
};

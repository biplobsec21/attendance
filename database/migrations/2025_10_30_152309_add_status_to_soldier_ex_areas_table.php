<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('soldier_ex_areas', function (Blueprint $table) {
            $table->enum('status', ['scheduled', 'active', 'completed'])->default('scheduled')->after('remarks');

            // Add indexes for better performance
            $table->index(['soldier_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down()
    {
        Schema::table('soldier_ex_areas', function (Blueprint $table) {
            $table->dropIndex(['soldier_id', 'status']);
            $table->dropIndex(['start_date', 'end_date']);
            $table->dropColumn('status');
        });
    }
};

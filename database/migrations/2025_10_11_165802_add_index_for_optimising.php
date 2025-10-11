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


        // Schema::table('soldier_services', function (Blueprint $table) {
        //     $table->index(['soldier_id', 'appointment_type'], 'idx_services_soldier_appointment');
        // });

        Schema::table('soldier_leave_applications', function (Blueprint $table) {
            $table->index(['soldier_id', 'application_current_status'], 'idx_leave_applications_soldier_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('soldiers', function (Blueprint $table) {
        //     // $table->dropIndex('idx_soldiers_is_on_leave');
        //     $table->dropIndex('idx_soldiers_is_sick');
        // });

        // Schema::table('soldier_services', function (Blueprint $table) {
        //     $table->dropIndex('idx_services_soldier_appointment');
        // });

        Schema::table('leave_applications', function (Blueprint $table) {
            $table->dropIndex('idx_soldier_leave_applications_soldier_status');
        });
    }
};

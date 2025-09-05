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
        Schema::create('soldier_leave_applications', function (Blueprint $table) {
            //
            $table->bigIncrements('id');
            $table->foreignId('soldier_id')->constrained('soldiers')->cascadeOnDelete();
            $table->unsignedInteger('leave_type_id');
            $table->text('reason')->nullable();
            $table->string('hard_copy')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('application_current_status', 100)->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('leave_type_id')->references('id')->on('leave_types')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soldier_leave_applications');
    }
};

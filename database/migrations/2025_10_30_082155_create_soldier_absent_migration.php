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
        Schema::create('soldier_absent', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('soldier_id')->constrained('soldiers')->cascadeOnDelete();
            $table->foreignId('absent_type_id')->constrained('absent_types')->cascadeOnDelete(); // Changed this line
            $table->text('reason')->nullable();
            $table->text('reject_reason')->nullable();
            $table->date('reject_status_date')->nullable();
            $table->string('hard_copy')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('absent_current_status', 100)->default('approved');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Remove the separate foreign key constraint since it's already defined above
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soldier_absent');
    }
};

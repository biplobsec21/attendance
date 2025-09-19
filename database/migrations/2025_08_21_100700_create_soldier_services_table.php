<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soldier_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('soldier_id');
            $table->string('appointments_name', 400)->nullable();
            $table->string('appointment_type', 20); // type could be currnent or previous
            $table->date('appointments_from_date')->nullable();
            $table->date('appointments_to_date',)->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->foreign('soldier_id')->references('id')->on('soldiers')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soldier_services');
    }
};

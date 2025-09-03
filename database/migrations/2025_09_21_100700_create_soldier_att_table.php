<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soldiers_att', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('soldier_id');
            $table->unsignedBigInteger('atts_id');

            $table->string('remarks', 400)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // $table->primary(['soldier_id', 'att_id']);
            $table->foreign('soldier_id')->references('id')->on('soldiers')->cascadeOnDelete();
            $table->foreign('atts_id')->references('id')->on('atts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soldiers_att');
    }
};

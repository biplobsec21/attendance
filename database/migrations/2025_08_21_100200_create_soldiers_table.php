<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soldiers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('full_name');
            $table->string('image')->nullable();
            $table->string('rank', 50)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('designation_id');
            $table->string('current_status', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('designation_id')->references('id')->on('designations');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soldiers');
    }
};





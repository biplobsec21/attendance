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
            $table->string('image')->nullable();
            $table->string('full_name');
            $table->string('army_no');

            $table->string('mobile', 20)->nullable();
            $table->enum('gender', ['Male', 'Female'])->nullable();
            $table->string('blood_group')->nullable();
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed'])->nullable();
            $table->unsignedInteger('num_boys')->default(0);
            $table->unsignedInteger('num_girls')->default(0);
            $table->string('village')->nullable();



            $table->text('permanent_address')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('rank_id')->constrained('ranks')->cascadeOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soldiers');
    }
};

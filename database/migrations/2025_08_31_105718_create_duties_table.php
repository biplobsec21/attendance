<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_duties_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duties', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('duty_name'); // For the duty name
            $table->time('start_time'); // For the start time
            $table->time('end_time'); // For the end time
            // $table->integer('manpower'); // For the number of personnel
            $table->text('remark')->nullable(); // For remarks, can be empty
            $table->string('status')->default('Active'); // For Active/Inactive status
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duties');
    }
};

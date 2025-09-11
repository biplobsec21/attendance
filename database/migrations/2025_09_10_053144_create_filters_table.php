<?php
// database/migrations/2025_09_10_000001_create_filters_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('filters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // user id
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filters');
    }
};

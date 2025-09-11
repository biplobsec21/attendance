<?php
// database/migrations/2025_09_10_000002_create_filter_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('filter_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filter_id')->constrained('filters')->onDelete('cascade');

            $table->string('table_name');   // soldiers, soldier_courses, etc.
            $table->string('column_name');  // blood_group, course_status, etc.
            $table->string('operator')->default('='); // =, !=, >, <, like, in
            $table->string('value_type')->default('text'); // input type: text, select, number, date
            $table->string('label'); // "Blood Group"
            $table->json('options')->nullable(); // for select dropdowns

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filter_items');
    }
};

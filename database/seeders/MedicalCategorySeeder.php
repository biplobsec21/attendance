<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicalCategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = ['A1', 'A2', 'B1', 'B2', 'C'];
        foreach ($names as $name) {
            DB::table('medical_categories')->updateOrInsert(['name' => $name], ['name' => $name]);
        }
    }
}














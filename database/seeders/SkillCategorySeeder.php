<?php

namespace Database\Seeders;

use App\Models\SkillCategory;
use Illuminate\Database\Seeder;

class SkillCategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Technical', 'Combat', 'Medical', 'Communications', 'Logistics'];
        foreach ($names as $name) {
            SkillCategory::firstOrCreate(['name' => $name]);
        }
    }
}













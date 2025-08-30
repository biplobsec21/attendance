<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skillsByCategory = [
            'Technical' => ['Networking', 'Electronics', 'Programming', 'Mechanical'],
            'Combat' => [
                'Marksmanship',
                'Hand-to-Hand Combat',
                'First Aid',
                'Survival Skills',
                'Explosives Handling',
                'Navigation & Map Reading',
                'Radio Communication',
                'Mechanical Maintenance',
                'Cybersecurity Basics',
                'Drone Operation'
            ],
            'Medical' => ['First Aid', 'Field Surgery'],
            'Communications' => ['Radio Operation', 'Signal Security'],
            'Logistics' => ['Supply Chain', 'Vehicle Maintenance'],
        ];

        foreach ($skillsByCategory as $categoryName => $skills) {
            $category = SkillCategory::firstOrCreate(['name' => $categoryName]);
            foreach ($skills as $skillName) {
                Skill::firstOrCreate([
                    'category_id' => $category->id,
                    'name' => $skillName,
                ]);
            }
        }
    }
}









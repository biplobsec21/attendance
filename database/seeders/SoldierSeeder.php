<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Skill;
use App\Models\Soldier;
use Illuminate\Database\Seeder;

class SoldierSeeder extends Seeder
{
    public function run(): void
    {
        $proficiencyLevels = ['Beginner', 'Intermediate', 'Advanced', 'Expert'];

        $skills = Skill::all();
        $courses = Course::all();

        Soldier::factory()
            ->count(50)
            ->create()
            ->each(function (Soldier $soldier) use ($skills, $courses, $proficiencyLevels) {
                $skillIds = $skills->random(rand(2, 5))->pluck('id')->all();
                $attachSkills = [];
                foreach ($skillIds as $skillId) {
                    $attachSkills[$skillId] = [
                        'proficiency_level' => $proficiencyLevels[array_rand($proficiencyLevels)],
                    ];
                }
                $soldier->skills()->attach($attachSkills);

                $courseIds = $courses->random(rand(1, 3))->pluck('id')->all();
                foreach ($courseIds as $courseId) {
                    $soldier->courses()->attach($courseId, [
                        'completion_date' => now()->subDays(rand(30, 2000))->toDateString(),
                        'remarks' => fake()->optional()->sentence(),
                    ]);
                }
            });
    }
}




















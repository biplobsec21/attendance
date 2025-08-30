<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $courses = [
            'Basic Training',
            'Infantry Tactics',
            'Military Leadership',
            'Advanced Weapons Training',
            'Airborne School',
            'Ranger School',
            'Sniper Training',
            'Combat Medic Course',
            'Logistics and Supply Chain Management',
            'Military Intelligence Analysis',
        ];

        foreach ($courses as $course) {
            Course::create([
                'name' => $course,
                'category' => 'General',   // ✅ add default category
                'description' => 'Basic military training course', // optional
            ]);
        }
    }
}









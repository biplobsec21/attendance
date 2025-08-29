<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RankSeeder::class,
            DistrictSeeder::class,
            EducationSeeder::class,
            CadreSeeder::class,
            MedicalCategorySeeder::class,
            CompanySeeder::class,
            DesignationSeeder::class,
            SkillCategorySeeder::class,
            SkillSeeder::class,
            CourseSeeder::class,
            SoldierSeeder::class,

        ]);
    }
}

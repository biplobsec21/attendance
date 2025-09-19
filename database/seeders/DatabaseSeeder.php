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
            AppointmentSeeder::class,
            AdminUserSeeder::class,
            DutiesTableSeeder::class,
            LeaveTypeSeeder::class,
            EreSeeder::class,
            AttSeeder::class,
            RankSeeder::class,
            DistrictSeeder::class,
            EducationSeeder::class,
            CadreSeeder::class,
            MedicalCategorySeeder::class,
            PermanentSicknessSeeder::class,
            CompanySeeder::class,
            DesignationSeeder::class,
            SkillCategorySeeder::class,
            SkillSeeder::class,
            CourseSeeder::class,
            SoldierSeeder::class,

            DutyRankTableSeeder::class,
            FilterSeeder::class,

        ]);
    }
}

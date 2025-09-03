<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['SSC', 'HSC', 'Diploma', 'Bachelor', 'Master'];
        foreach ($names as $name) {
            DB::table('educations')->updateOrInsert(['name' => $name], ['name' => $name]);
        }
    }
}














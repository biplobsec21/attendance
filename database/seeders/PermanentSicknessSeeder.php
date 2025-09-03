<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermanentSicknessSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['None', 'Hypertension', 'Diabetes', 'Asthma', 'Chronic Back Pain', 'Hearing Loss', 'Vision Impairment'];
        foreach ($names as $name) {
            DB::table('permanent_sickness')->updateOrInsert(['name' => $name], ['name' => $name]);
        }
    }
}

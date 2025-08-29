<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eres = [
            'Instructor - BMA',
            'Instructor - SI&T',
            'Instructor - Other School',
            'Staff - AHQ',
            'Staff - Division HQ',
            'Staff - Brigade HQ',
            'UN Mission',
            'Aide-de-Camp (ADC)',
            'MS to President/PM',
        ];

        foreach ($eres as $ere) {
            DB::table('eres')->insert([
                'name' => $ere,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

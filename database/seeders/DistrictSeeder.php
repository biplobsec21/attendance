<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Dhaka',
            'Chattogram',
            'Rajshahi',
            'Khulna',
            'Barishal',
            'Sylhet',
            'Rangpur',
            'Mymensingh'
        ];
        foreach ($names as $name) {
            DB::table('districts')->updateOrInsert(['name' => $name], ['name' => $name]);
        }
    }
}








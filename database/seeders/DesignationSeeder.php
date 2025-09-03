<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Private',
            'Corporal',
            'Sergeant',
            'Staff Sergeant',
            'Warrant Officer',
            'Lieutenant',
            'Captain',
            'Major',
            'Lieutenant Colonel',
            'Colonel',
            'Brigadier General',
            'Major General',
            'Lieutenant General',
            'General'
        ];
        foreach ($names as $name) {
            Designation::firstOrCreate(['name' => $name]);
        }
    }
}














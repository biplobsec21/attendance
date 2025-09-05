<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['MEDICAL LEAVE', 'EARNED LEAVE', 'Annual leave'];
        foreach ($names as $name) {
            DB::table('leave_types')->updateOrInsert(['name' => $name], ['name' => $name]);
        }
    }
}

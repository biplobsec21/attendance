<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RankSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Private', 'Lance Corporal', 'Corporal', 'Sergeant', 'Warrant Officer'];
        foreach ($names as $name) {
            DB::table('ranks')->updateOrInsert(['name' => $name], ['name' => $name]);
        }
    }
}








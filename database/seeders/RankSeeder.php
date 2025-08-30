<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rank;

class RankSeeder extends Seeder
{
    public function run(): void
    {
        $ranks = [
            'Private',
            'Lance Corporal',
            'Corporal',
            'Sergeant',
            'Staff Sergeant',
            'Warrant Officer',
            'Lieutenant',
            'Captain',
            'Major',
            'Colonel',
            'General',
        ];

        foreach ($ranks as $rank) {
            Rank::firstOrCreate(['name' => $rank], ['status' => true]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $atts = [
            'Attachment with BMA',
            'Attachment with other unit',
            'Attachment with DGFI',
            'Attachment with NSI',
            'Attachment with Civil Org.',
        ];

        foreach ($atts as $att) {
            DB::table('atts')->insert([
                'name' => $att,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

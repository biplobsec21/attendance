<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cadre;

class CadreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cadres = [
            'Infantry',
            'Artillery',
            'Armoured',
            'Signals',
            'Engineers',
            'Medical',
            'Education',
            'Ordnance',
            'Supply & Transport',
            'Military Police',
        ];

        foreach ($cadres as $cadre) {
            Cadre::updateOrCreate(['name' => $cadre]);
        }
    }
}

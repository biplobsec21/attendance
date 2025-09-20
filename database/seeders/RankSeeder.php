<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rank;

class RankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ranks = [
            // Officer Ranks
            'Cadet'              => 'OFFICER',
            'Second Lieutenant'  => 'OFFICER',
            'Lieutenant'         => 'OFFICER',
            'Captain'            => 'OFFICER',
            'Major'              => 'OFFICER',
            'Lieutenant Colonel' => 'OFFICER',
            'Colonel'            => 'OFFICER',
            'Brigadier General'  => 'OFFICER',
            'Major General'      => 'OFFICER',
            'Lieutenant General' => 'OFFICER',
            'General'            => 'OFFICER',

            // Junior Commissioned Officer (JCO) Ranks
            'Warrant Officer'        => 'JCO',
            'Senior Warrant Officer' => 'JCO',
            'Master Warrant Officer' => 'JCO',

            // Other Ranks (OR)
            'Sainik'                 => 'OR',
            'Lance Corporal'         => 'OR',
            'Corporal'               => 'OR',
            'Sergeant'               => 'OR',

            // Religious Commissioned Officer (RCO) - a specific officer type
            'Religious Teacher'      => 'RCO',

        ];

        foreach ($ranks as $name => $type) {
            Rank::firstOrCreate(
                ['name' => $name],
                ['type' => $type, 'status' => true]
            );
        }
    }
}

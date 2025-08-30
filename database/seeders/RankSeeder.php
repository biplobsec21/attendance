<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rank;

class RankSeeder extends Seeder
{
    public function run(): void
    {
        $ranks = [
            // Commissioned Officers
            'Second Lieutenant'  => 'Commissioned Officers',
            'Lieutenant'         => 'Commissioned Officers',
            'Captain'            => 'Commissioned Officers',
            'Major'              => 'Commissioned Officers',
            'Lieutenant Colonel' => 'Commissioned Officers',
            'Colonel'            => 'Commissioned Officers',
            'Brigadier General'  => 'Commissioned Officers',
            'Major General'      => 'Commissioned Officers',
            'Lieutenant General' => 'Commissioned Officers',
            'General'            => 'Commissioned Officers',
            'Field Marshal'      => 'Commissioned Officers',

            // JCOs & NCOs
            'Master Warrant Officer' => 'JCOs & NCOs',
            'Senior Warrant Officer' => 'JCOs & NCOs',
            'Warrant Officer'        => 'JCOs & NCOs',
            'Sergeant'               => 'JCOs & NCOs',
            'Corporal'               => 'JCOs & NCOs',
            'Lance Corporal'         => 'JCOs & NCOs',
            'Sainik'                 => 'JCOs & NCOs',
        ];

        foreach ($ranks as $name => $type) {
            Rank::firstOrCreate(
                ['name' => $name],
                ['type' => $type, 'status' => true]
            );
        }
    }
}

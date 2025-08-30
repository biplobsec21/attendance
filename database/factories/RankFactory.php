<?php

namespace Database\Factories;

use App\Models\Rank;
use Illuminate\Database\Eloquent\Factories\Factory;

class RankFactory extends Factory
{
    protected $model = Rank::class;

    public function definition(): array
    {
        $ranks = [
            // Commissioned Officers
            'Second Lieutenant' => 'Commissioned Officers',
            'Lieutenant'        => 'Commissioned Officers',
            'Captain'           => 'Commissioned Officers',
            'Major'             => 'Commissioned Officers',
            'Lieutenant Colonel' => 'Commissioned Officers',
            'Colonel'           => 'Commissioned Officers',
            'Brigadier General' => 'Commissioned Officers',
            'Major General'     => 'Commissioned Officers',
            'Lieutenant General' => 'Commissioned Officers',
            'General'           => 'Commissioned Officers',
            'Field Marshal'     => 'Commissioned Officers',

            // JCOs & NCOs
            'Master Warrant Officer' => 'JCOs & NCOs',
            'Senior Warrant Officer' => 'JCOs & NCOs',
            'Warrant Officer'        => 'JCOs & NCOs',
            'Sergeant'               => 'JCOs & NCOs',
            'Corporal'               => 'JCOs & NCOs',
            'Lance Corporal'         => 'JCOs & NCOs',
            'Sainik'                 => 'JCOs & NCOs',
        ];

        $name = $this->faker->unique()->randomElement(array_keys($ranks));

        return [
            'name'   => $name,
            'type'   => $ranks[$name],
            'status' => true,
        ];
    }
}

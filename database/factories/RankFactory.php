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

        $name = $this->faker->unique()->randomElement(array_keys($ranks));

        return [
            'name'   => $name,
            'type'   => $ranks[$name],
            'status' => true,
        ];
    }
}

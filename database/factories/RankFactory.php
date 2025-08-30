<?php

namespace Database\Factories;

use App\Models\Rank;
use Illuminate\Database\Eloquent\Factories\Factory;

class RankFactory extends Factory
{
    protected $model = Rank::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
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
            ]),
            'status' => true, // if your Rank table has active/inactive status
        ];
    }
}

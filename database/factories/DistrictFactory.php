<?php

namespace Database\Factories;

use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

class DistrictFactory extends Factory
{
    protected $model = District::class;

    public function definition(): array
    {
        // If you want a fixed set, use the seeder below and
        // keep this factory minimal for ad-hoc tests:
        return [
            'name' => $this->faker->unique()->city(), // or 'district_name' if your column is named differently
            // 'status' => true, // uncomment if your table has status
        ];
    }
}

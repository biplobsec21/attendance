<?php

namespace Database\Factories;

use App\Models\Soldier;
use App\Models\Company;
use App\Models\Rank;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

class SoldierFactory extends Factory
{
    protected $model = Soldier::class;

    public function definition(): array
    {
        return [
            'image' => 'images/default-avatar.png',
            'full_name' => $this->faker->name(),
            'army_no' => strtoupper($this->faker->bothify('ARMY-###??')),
            'company_id' => Company::inRandomOrder()->first()->id ?? Company::factory(),
            'rank_id' => Rank::inRandomOrder()->first()->id ?? Rank::factory(),
            'is_leave' => fake()->boolean(),
            'mobile' => $this->faker->optional()->phoneNumber(),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'blood_group' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
            'marital_status' => $this->faker->randomElement(['Single', 'Married', 'Divorced', 'Widowed']),
            'num_boys' => $this->faker->numberBetween(0, 3),
            'num_girls' => $this->faker->numberBetween(0, 3),
            'village' => $this->faker->optional()->citySuffix(),
            'district_id' => District::factory(),
            'permanent_address' => $this->faker->optional()->address(),
            'status' => $this->faker->boolean(90),
            'personal_completed' => true,
        ];
    }
}

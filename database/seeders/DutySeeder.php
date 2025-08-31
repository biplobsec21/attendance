<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Duty; // Import the Duty model

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Duty>
 */
class DutyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Duty::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate random start and end times
        $startTime = $this->faker->time('H:i');
        $endTime = date('H:i', strtotime('+'. $this->faker->numberBetween(1, 8) .' hours', strtotime($startTime)));

        return [
            'duty_name' => $this->faker->randomElement(['Guard Duty', 'Patrol Duty', 'Kitchen Duty', 'Clerk Duty', 'Range Safety']),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'manpower' => $this->faker->numberBetween(2, 10),
            'remark' => $this->faker->optional()->sentence(),
            'status' => $this->faker->randomElement(['Active', 'Inactive']),
        ];
    }
}
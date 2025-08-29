<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $categories = ['Cadre', 'Special Training', 'Leadership', 'Technical'];
        return [
            'name' => ucfirst($this->faker->unique()->words(3, true)),
            'category' => $this->faker->randomElement($categories),
            'description' => $this->faker->optional()->paragraph(),
        ];
    }
}












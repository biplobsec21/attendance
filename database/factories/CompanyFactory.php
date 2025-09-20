<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * The list of predefined companies.
     *
     * @var array
     */
    protected static $companies = [
        'A',
        'B',
        'C',
        'D',
        'Shadar',
        'Joined',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(self::$companies),
        ];
    }
}

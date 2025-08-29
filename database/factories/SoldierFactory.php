<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Designation;
use App\Models\Soldier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<\App\Models\Soldier>
 */
class SoldierFactory extends Factory
{
    protected $model = Soldier::class;

    public function definition(): array
    {
        $rankOptions = ['Private', 'Corporal', 'Sergeant', 'Lieutenant', 'Captain'];
        $statusOptions = ['Active', 'On Leave', 'Reserve', 'Training'];

        $companyId = Company::query()->inRandomOrder()->value('id') ?? Company::factory();
        $designationId = Designation::query()->inRandomOrder()->value('id') ?? Designation::factory();

        return [
            'full_name' => $this->faker->name(),
            'image' => $this->faker->optional()->imageUrl(256, 256, 'people', true),
            'rank' => $this->faker->randomElement($rankOptions),
            'mobile' => $this->faker->optional()->e164PhoneNumber(),
            'company_id' => $companyId,
            'designation_id' => $designationId,
            'current_status' => $this->faker->randomElement($statusOptions),
        ];
    }
}

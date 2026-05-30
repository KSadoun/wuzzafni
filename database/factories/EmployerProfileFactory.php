<?php

namespace Database\Factories;

use App\Models\EmployerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmployerProfile>
 */
class EmployerProfileFactory extends Factory
{
    protected $model = EmployerProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->employer(),
            'company_name' => fake()->company(),
            'company_description' => fake()->paragraphs(2, true),
            'company_logo' => null,
            'company_website' => fake()->url(),
            'field' => fake()->randomElement([
                'Software',
                'Fintech',
                'Healthcare',
                'E-commerce',
                'Education',
                'Media',
            ]),
            'company_size' => fake()->randomElement(['1-10', '11-50', '51-200', '201-500', '500+']),
            'location' => fake()->city(),
        ];
    }
}

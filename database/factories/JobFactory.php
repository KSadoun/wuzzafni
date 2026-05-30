<?php

namespace Database\Factories;

use App\Models\EmployerProfile;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        $salaryMin = fake()->numberBetween(40000, 90000);
        $salaryMax = fake()->numberBetween($salaryMin + 5000, 160000);

        return [
            'employer_profile_id' => EmployerProfile::factory(),
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraphs(3, true),
            'responsibilities' => fake()->paragraphs(2, true),
            'requirements' => fake()->paragraphs(2, true),
            'benefits' => fake()->paragraphs(1, true),
            'salary_min' => $salaryMin,
            'salary_max' => $salaryMax,
            'location' => fake()->city(),
            'work_type' => fake()->randomElement(['remote', 'onsite', 'hybrid']),
            'experience_level' => fake()->randomElement(['entry', 'mid', 'senior', 'lead']),
            'application_deadline' => fake()->dateTimeBetween('now', '+2 months'),
            'status' => fake()->randomElement(['active', 'active', 'active', 'closed', 'draft']),
            'views_count' => fake()->numberBetween(0, 500),
            'applications_count' => fake()->numberBetween(0, 50),
        ];
    }
}

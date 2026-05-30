<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\EmployerProfile;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'job_id' => Job::factory(),
            'candidate_profile_id' => CandidateProfile::factory(),
            'employer_profile_id' => EmployerProfile::factory(),
            'resume' => null,
            'cover_letter' => fake()->paragraphs(2, true),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'status' => fake()->randomElement(['pending', 'reviewed', 'accepted', 'rejected']),
            'applied_at' => fake()->dateTimeBetween('-2 months', 'now'),
        ];
    }
}

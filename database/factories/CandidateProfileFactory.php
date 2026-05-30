<?php

namespace Database\Factories;

use App\Models\CandidateProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CandidateProfile>
 */
class CandidateProfileFactory extends Factory
{
    protected $model = CandidateProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->candidate(),
            'resume' => null,
            'phone' => fake()->phoneNumber(),
            'linkedin_url' => fake()->url(),
            'github_url' => fake()->url(),
            'bio' => fake()->paragraphs(2, true),
            'experience_level' => fake()->randomElement(['entry', 'mid', 'senior', 'lead']),
            'years_of_experience' => fake()->numberBetween(0, 12),
            'current_position' => fake()->jobTitle(),
            'location' => fake()->city(),
        ];
    }
}

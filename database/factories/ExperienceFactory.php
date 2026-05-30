<?php

namespace Database\Factories;

use App\Models\CandidateProfile;
use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    protected $model = Experience::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 years', '-1 years');
        $endDate = fake()->boolean(70) ? fake()->dateTimeBetween($startDate, 'now') : null;

        return [
            'candidate_profile_id' => CandidateProfile::factory(),
            'company_name' => fake()->company(),
            'position' => fake()->jobTitle(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'description' => fake()->paragraphs(2, true),
        ];
    }
}

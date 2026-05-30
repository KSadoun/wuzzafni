<?php

namespace Database\Factories;

use App\Models\CandidateProfile;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        return [
            'candidate_profile_id' => CandidateProfile::factory(),
            'skill_name' => fake()->randomElement([
                'PHP',
                'Laravel',
                'JavaScript',
                'Vue.js',
                'React',
                'TypeScript',
                'MySQL',
                'PostgreSQL',
                'Docker',
                'AWS',
            ]),
        ];
    }
}

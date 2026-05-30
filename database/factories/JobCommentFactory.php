<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\JobComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobComment>
 */
class JobCommentFactory extends Factory
{
    protected $model = JobComment::class;

    public function definition(): array
    {
        return [
            'job_id' => Job::factory(),
            'user_id' => User::factory(),
            'content' => fake()->sentence(16),
        ];
    }
}

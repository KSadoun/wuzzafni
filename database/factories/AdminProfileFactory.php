<?php

namespace Database\Factories;

use App\Models\AdminProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdminProfile>
 */
class AdminProfileFactory extends Factory
{
    protected $model = AdminProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->admin(),
            'permissions' => fake()->randomElements([
                'manage_users',
                'manage_jobs',
                'manage_payments',
                'view_reports',
                'moderate_comments',
            ], fake()->numberBetween(2, 4)),
        ];
    }
}

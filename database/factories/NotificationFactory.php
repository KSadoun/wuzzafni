<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'application_id' => Application::factory(),
            'type' => fake()->randomElement(['application_status', 'new_application', 'payment_update']),
            'title' => fake()->sentence(4),
            'message' => fake()->paragraph(),
            'is_read' => fake()->boolean(30),
        ];
    }
}

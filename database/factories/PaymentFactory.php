<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\EmployerProfile;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'paid', 'failed']);

        return [
            'employer_profile_id' => EmployerProfile::factory(),
            'application_id' => Application::factory(),
            'amount' => fake()->randomFloat(2, 19.99, 199.99),
            'payment_method' => fake()->randomElement(['card', 'bank_transfer', 'wallet']),
            'payment_status' => $status,
            'transaction_id' => $status === 'paid' ? Str::upper(Str::random(12)) : null,
        ];
    }
}

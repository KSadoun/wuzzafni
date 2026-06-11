<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(null, $input['role'] ?? null),
            'password' => $this->passwordRules(),
        ])->validate();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($input) {
            $user = User::create([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'role' => $input['role'],
            ]);

            if (config('app.env') === 'local') {
                $user->markEmailAsVerified();
            }

            if ($input['role'] === 'candidate') {
                $user->candidateProfile()->create([]);
            } elseif ($input['role'] === 'employer') {
                $user->employerProfile()->create([
                    'company_name' => $input['company_name'],
                ]);
            }

            return $user;
        });
    }
}

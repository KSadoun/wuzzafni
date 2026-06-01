<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function profileRules(?int $userId = null, ?string $role = null): array
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => $this->emailRules($userId),
        ];

        if ($role) {
            $rules['role'] = ['required', 'string', Rule::in(['candidate', 'employer'])];
        }

        $activeRole = $role ?: ($userId ? User::find($userId)?->role : null);

        if ($activeRole === 'candidate') {
            $rules = array_merge($rules, [
                'phone' => ['nullable', 'string', 'max:30'],
                'linkedin_url' => ['nullable', 'url', 'max:255'],
                'github_url' => ['nullable', 'url', 'max:255'],
                'bio' => ['nullable', 'string', 'max:1000'],
                'experience_level' => ['nullable', 'string', Rule::in(['entry', 'mid', 'senior', 'lead'])],
                'years_of_experience' => ['nullable', 'integer', 'min:0'],
                'current_position' => ['nullable', 'string', 'max:255'],
                'location' => ['nullable', 'string', 'max:255'],
            ]);
        } elseif ($activeRole === 'employer') {
            $rules = array_merge($rules, [
                'company_name' => ['required', 'string', 'max:255'],
                'company_description' => ['nullable', 'string', 'max:1000'],
                'company_website' => ['nullable', 'url', 'max:255'],
                'field' => ['nullable', 'string', 'max:255'],
                'company_size' => ['nullable', 'string', 'max:255'],
                'location' => ['nullable', 'string', 'max:255'],
            ]);
        }

        return $rules;
    }

    /**
     * Get the validation rules used to validate user names.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function nameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * Get the validation rules used to validate user emails.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function emailRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            $userId === null
                ? Rule::unique(User::class)
                : Rule::unique(User::class)->ignore($userId),
        ];
    }
}

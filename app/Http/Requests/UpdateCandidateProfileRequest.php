<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'candidate';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // Max 5MB
            'phone' => 'nullable|string|max:20',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'bio' => 'nullable|string',
            'experience_level' => 'nullable|string|max:50',
            'years_of_experience' => 'nullable|integer|min:0|max:50',
            'current_position' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
        ];
    }
}

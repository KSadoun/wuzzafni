<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CandidateProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
                'avatar' => $this->user->avatar ? Storage::url($this->user->avatar) : null,
            ],
            'resume_url' => $this->resume ? Storage::url($this->resume) : null,
            'phone' => $this->phone,
            'linkedin_url' => $this->linkedin_url,
            'github_url' => $this->github_url,
            'bio' => $this->bio,
            'experience_level' => $this->experience_level,
            'years_of_experience' => $this->years_of_experience,
            'current_position' => $this->current_position,
            'location' => $this->location,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ApplicationResource extends JsonResource
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
            'job' => [
                'id' => $this->job->id ?? null,
                'title' => $this->job->title ?? null,
                'employerProfile' => $this->when(
                    $this->relationLoaded('job') && $this->job?->relationLoaded('employerProfile'),
                    fn () => [
                        'company_name' => $this->job->employerProfile->company_name ?? null,
                    ]
                ),
            ],
            'resume_url' => $this->resume ? Storage::url($this->resume) : null,
            'cover_letter' => $this->cover_letter,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => $this->status,
            'applied_at' => $this->applied_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

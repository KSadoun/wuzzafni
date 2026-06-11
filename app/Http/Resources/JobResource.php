<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'responsibilities' => $this->responsibilities,
            'requirements' => $this->requirements,
            'benefits' => $this->benefits,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'location' => $this->location,
            'work_type' => $this->work_type,
            'experience_level' => $this->experience_level,
            'application_deadline' => $this->application_deadline ? $this->application_deadline->format('Y-m-d') : null,
            'status' => $this->status,
            'views_count' => $this->views_count,
            'applications_count' => $this->applications_count,
            'employer' => [
                'id' => $this->employerProfile?->id,
                'company_name' => $this->employerProfile?->company_name,
                'logo' => $this->employerProfile?->company_logo,
            ],
            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                    ];
                });
            }),
            'technologies' => $this->whenLoaded('technologies', function () {
                return $this->technologies->map(function ($tech) {
                    return [
                        'id' => $tech->id,
                        'name' => $tech->name,
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

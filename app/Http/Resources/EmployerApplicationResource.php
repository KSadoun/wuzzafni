<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class EmployerApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'job_id' => $this->job_id,
            'job' => [
                'id' => $this->job->id ?? null,
                'title' => $this->job->title ?? null,
                'experience_level' => $this->job->experience_level ?? null,
            ],
            'candidate' => [
                'name' => $this->candidateProfile?->user?->name,
                'email' => $this->email ?? $this->candidateProfile?->user?->email,
                'phone' => $this->phone ?? $this->candidateProfile?->phone,
                'experience_level' => $this->candidateProfile?->experience_level,
                'current_position' => $this->candidateProfile?->current_position,
            ],
            'resume_url' => $this->resume ? Storage::url($this->resume) : null,
            'cover_letter' => $this->cover_letter,
            'status' => $this->status,
            'applied_at' => $this->applied_at,
            'payment' => $this->whenLoaded('payment', fn () => [
                'id' => $this->payment->id,
                'amount' => $this->payment->amount,
                'payment_status' => $this->payment->payment_status,
                'payment_method' => $this->payment->payment_method,
            ]),
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use App\Notifications\NewApplicationNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class ApplicationService
{
    /**
     * Apply for a job.
     */
    public function apply(User $candidateUser, Job $job, array $data): Application
    {
        $candidateProfile = $candidateUser->candidateProfile;

        if (! $candidateProfile) {
            throw ValidationException::withMessages([
                'profile' => ['Candidate profile is required before applying.'],
            ]);
        }

        // Check deadline
        if ($job->application_deadline && $job->application_deadline < now()->startOfDay()) {
            throw ValidationException::withMessages([
                'job' => ['The application deadline for this job has passed.'],
            ]);
        }

        // Prevent duplicate applications
        $alreadyApplied = Application::where('job_id', $job->id)
            ->where('candidate_profile_id', $candidateProfile->id)
            ->exists();

        if ($alreadyApplied) {
            throw ValidationException::withMessages([
                'job' => ['You have already applied for this job.'],
            ]);
        }

        $resumePath = null;

        if (isset($data['resume']) && $data['resume'] instanceof UploadedFile) {
            $resumePath = $data['resume']->store('resumes/applications', 'public');
        } elseif (! empty($data['use_existing_resume']) && $candidateProfile->resume) {
            $resumePath = $candidateProfile->resume;
        } else {
            throw ValidationException::withMessages([
                'resume' => ['A resume is required to apply. Please upload one or use your profile resume.'],
            ]);
        }

        $application = Application::create([
            'job_id' => $job->id,
            'candidate_profile_id' => $candidateProfile->id,
            'employer_profile_id' => $job->employer_profile_id,
            'resume' => $resumePath,
            'cover_letter' => $data['cover_letter'] ?? null,
            'phone' => $data['phone'] ?? $candidateProfile->phone,
            'email' => $data['email'] ?? $candidateUser->email,
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        // Notify employer
        $employerUser = $job->employerProfile->user;
        if ($employerUser) {
            $employerUser->notify(new NewApplicationNotification($application));
        }

        return $application;
    }

    /**
     * Cancel an application.
     */
    public function cancel(Application $application): Application
    {
        if ($application->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => ['Only pending applications can be cancelled.'],
            ]);
        }

        $application->update(['status' => 'cancelled']);

        return $application;
    }
}

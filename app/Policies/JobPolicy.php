<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;

class JobPolicy
{
    public function viewAnalytics(User $user, Job $job): bool
    {
        return $user->role === 'employer'
            && $user->employerProfile
            && $user->employerProfile->id === $job->employer_profile_id;
    }

    public function manageApplications(User $user, Job $job): bool
    {
        return $this->viewAnalytics($user, $job);
    }
}

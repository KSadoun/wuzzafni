<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Application $application): bool
    {
        // Only candidate who created it or the employer can view
        if ($user->role === 'candidate' && $user->candidateProfile && $user->candidateProfile->id === $application->candidate_profile_id) {
            return true;
        }

        if ($user->role === 'employer' && $user->employerProfile && $user->employerProfile->id === $application->employer_profile_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can cancel the application.
     */
    public function cancel(User $user, Application $application): bool
    {
        return $user->role === 'candidate' 
            && $user->candidateProfile 
            && $user->candidateProfile->id === $application->candidate_profile_id 
            && $application->status === 'pending';
    }

    public function accept(User $user, Application $application): bool
    {
        return $user->role === 'employer'
            && $user->employerProfile
            && $user->employerProfile->id === $application->employer_profile_id
            && in_array($application->status, ['pending', 'reviewed']);
    }
}

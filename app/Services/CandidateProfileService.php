<?php

namespace App\Services;

use App\Models\CandidateProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CandidateProfileService
{
    /**
     * Get or create the candidate profile for the user.
     */
    public function getProfile(User $user): CandidateProfile
    {
        return $user->candidateProfile()->firstOrCreate([
            'user_id' => $user->id,
        ]);
    }

    /**
     * Update the candidate profile.
     */
    public function updateProfile(User $user, array $data): CandidateProfile
    {
        $profile = $this->getProfile($user);

        if (isset($data['resume']) && $data['resume'] instanceof UploadedFile) {
            // Delete old resume if exists
            if ($profile->resume) {
                Storage::disk('public')->delete($profile->resume);
            }

            // Store new resume
            $path = $data['resume']->store('resumes', 'public');
            $data['resume'] = $path;
        }

        $profile->update($data);

        return $profile;
    }
}

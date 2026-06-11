<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileDeleteRequest;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        DB::transaction(function () use ($user, $validated) {
            $user->fill([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
            ]);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            if ($user->role === 'candidate') {
                $user->candidateProfile()->update([
                    'phone' => $validated['phone'] ?? null,
                    'linkedin_url' => $validated['linkedin_url'] ?? null,
                    'github_url' => $validated['github_url'] ?? null,
                    'bio' => $validated['bio'] ?? null,
                    'experience_level' => $validated['experience_level'] ?? null,
                    'years_of_experience' => $validated['years_of_experience'] ?? null,
                    'current_position' => $validated['current_position'] ?? null,
                    'location' => $validated['location'] ?? null,
                ]);
            } elseif ($user->role === 'employer') {
                $user->employerProfile()->update([
                    'company_name' => $validated['company_name'],
                    'company_description' => $validated['company_description'] ?? null,
                    'company_website' => $validated['company_website'] ?? null,
                    'field' => $validated['field'] ?? null,
                    'company_size' => $validated['company_size'] ?? null,
                    'location' => $validated['location'] ?? null,
                ]);
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Profile updated.')]);

        return to_route('profile.edit');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(ProfileDeleteRequest $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

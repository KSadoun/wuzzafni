<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => function () use ($request) {
                    $user = $request->user();
                    if ($user) {
                        if ($user->role === 'candidate') {
                            $user->load('candidateProfile');
                        } elseif ($user->role === 'employer') {
                            $user->load('employerProfile');
                        } elseif ($user->role === 'admin') {
                            $user->load('adminProfile');
                        }
                        // Add computed 'name' for components that expect it
                        $user->name = trim($user->first_name.' '.$user->last_name);
                    }

                    return $user;
                },
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'dashboardUrl' => fn () => $request->user()?->dashboardPath(),
        ];
    }
}

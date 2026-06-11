<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\Job;
use App\Services\ApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    protected ApplicationService $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    /**
     * Store a newly created application.
     * Response: { message, application }
     */
    public function store(StoreApplicationRequest $request, Job $job): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $application = $this->applicationService->apply($user, $job, $data);
        $application->load('job.employerProfile');

        return response()->json([
            'message' => 'Application submitted successfully',
            'application' => new ApplicationResource($application)
        ], 201);
    }

    /**
     * Display a paginated listing of applications for the candidate.
     * Response: { data: Application[], meta: {} }
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'candidate' || !$user->candidateProfile) {
            return response()->json(['message' => 'Unauthorized or missing candidate profile.'], 403);
        }

        $applications = Application::with(['job.employerProfile'])
            ->where('candidate_profile_id', $user->candidateProfile->id)
            ->latest('applied_at')
            ->paginate(15);

        // Return standard paginated format: { data: [], meta: {} }
        return response()->json(
            ApplicationResource::collection($applications)->response()->getData(true)
        );
    }

    /**
     * Display a single application.
     */
    public function show(Application $application): JsonResponse
    {
        // Ensure the authenticated user owns this application
        $user = request()->user();
        if ($user->candidateProfile?->id !== $application->candidate_profile_id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $application->load('job.employerProfile');

        return response()->json([
            'data' => new ApplicationResource($application)
        ]);
    }

    /**
     * Cancel the application.
     */
    public function cancel(Application $application): JsonResponse
    {
        // Ensure the authenticated user owns this application
        $user = request()->user();
        if ($user->candidateProfile?->id !== $application->candidate_profile_id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $application = $this->applicationService->cancel($application);

        return response()->json([
            'message' => 'Application cancelled successfully',
            'application' => new ApplicationResource($application)
        ]);
    }
}


<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCandidateProfileRequest;
use App\Http\Resources\CandidateProfileResource;
use App\Services\CandidateProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CandidateProfileController extends Controller
{
    protected CandidateProfileService $candidateProfileService;

    public function __construct(CandidateProfileService $candidateProfileService)
    {
        $this->candidateProfileService = $candidateProfileService;
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->role !== 'candidate') {
            return response()->json(['message' => 'Unauthorized. Must be a candidate.'], 403);
        }

        $profile = $this->candidateProfileService->getProfile($user);
        $profile->load('user');

        return response()->json([
            'message' => 'Profile retrieved successfully',
            'data' => new CandidateProfileResource($profile)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCandidateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $profile = $this->candidateProfileService->updateProfile($user, $data);
        $profile->load('user');

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => new CandidateProfileResource($profile)
        ]);
    }
}

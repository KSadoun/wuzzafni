<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Services\JobSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    protected JobSearchService $jobSearchService;

    public function __construct(JobSearchService $jobSearchService)
    {
        $this->jobSearchService = $jobSearchService;
    }

    /**
     * Display a listing of jobs (with filters + pagination).
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'keyword',
            'location',
            'category_id',
            'technology_ids',
            'work_type',
            'salary_min',
            'salary_max',
            'date_posted',
            'sort',
            'per_page'
        ]);

        $jobs = $this->jobSearchService->search($filters);

        return response()->json(
            JobResource::collection($jobs)->response()->getData(true)
        );
    }

    /**
     * Display a single job's details.
     */
    public function show(Job $job): JsonResponse
    {
        $job->load(['employerProfile', 'categories', 'technologies']);

        $hasApplied = false;
        if (auth()->check()) {
            $hasApplied = $job->applications()
                ->where('candidate_profile_id', function ($q) {
                    $q->select('id')->from('candidate_profiles')->where('user_id', auth()->id());
                })
                ->exists();
        }

        $data = (new JobResource($job))->toArray(request());
        $data['has_applied'] = $hasApplied;

        return response()->json(['data' => $data]);
    }
}


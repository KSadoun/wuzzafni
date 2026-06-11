<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Models\Category;
use App\Models\Technology;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployerJobController extends Controller
{
    /**
     * Display a listing of the employer's jobs.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->role !== 'employer' || !$user->employerProfile) {
            return response()->json(['message' => 'Unauthorized or missing employer profile.'], 403);
        }

        $jobs = Job::with(['categories', 'technologies', 'employerProfile'])
            ->where('employer_profile_id', $user->employerProfile->id)
            ->latest()
            ->paginate(10);

        return response()->json(
            JobResource::collection($jobs)->response()->getData(true)
        );
    }

    /**
     * Store a newly created job.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->role !== 'employer' || !$user->employerProfile) {
            return response()->json(['message' => 'Unauthorized or missing employer profile.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'location' => 'nullable|string|max:255',
            'work_type' => ['required', Rule::in(['remote', 'onsite', 'hybrid'])],
            'experience_level' => ['nullable', Rule::in(['entry', 'mid', 'senior', 'lead'])],
            'application_deadline' => 'nullable|date|after:today',
            'status' => ['required', Rule::in(['active', 'closed', 'draft'])],
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'technology_ids' => 'nullable|array',
            'technology_ids.*' => 'exists:technologies,id',
        ]);

        $job = DB::transaction(function () use ($validated, $user) {
            $job = Job::create([
                'employer_profile_id' => $user->employerProfile->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'responsibilities' => $validated['responsibilities'] ?? null,
                'requirements' => $validated['requirements'] ?? null,
                'benefits' => $validated['benefits'] ?? null,
                'salary_min' => $validated['salary_min'] ?? null,
                'salary_max' => $validated['salary_max'] ?? null,
                'location' => $validated['location'] ?? null,
                'work_type' => $validated['work_type'],
                'experience_level' => $validated['experience_level'] ?? null,
                'application_deadline' => $validated['application_deadline'] ?? null,
                'status' => $validated['status'],
                'views_count' => 0,
                'applications_count' => 0
            ]);

            if (!empty($validated['category_ids'])) {
                $job->categories()->attach($validated['category_ids']);
            }

            if (!empty($validated['technology_ids'])) {
                $job->technologies()->attach($validated['technology_ids']);
            }

            return $job;
        });

        $job->load(['categories', 'technologies', 'employerProfile']);

        return response()->json([
            'message' => 'Job posted successfully.',
            'job' => new JobResource($job)
        ], 201);
    }

    /**
     * Show a single job.
     */
    public function show(Request $request, Job $job): JsonResponse
    {
        $user = $request->user();
        if ($job->employer_profile_id !== $user->employerProfile?->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $job->load(['categories', 'technologies', 'employerProfile']);
        return response()->json(['data' => new JobResource($job)]);
    }

    /**
     * Update the specified job.
     */
    public function update(Request $request, Job $job): JsonResponse
    {
        $user = $request->user();
        if ($job->employer_profile_id !== $user->employerProfile?->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'location' => 'nullable|string|max:255',
            'work_type' => ['required', Rule::in(['remote', 'onsite', 'hybrid'])],
            'experience_level' => ['nullable', Rule::in(['entry', 'mid', 'senior', 'lead'])],
            'application_deadline' => 'nullable|date|after:today',
            'status' => ['required', Rule::in(['active', 'closed', 'draft'])],
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'technology_ids' => 'nullable|array',
            'technology_ids.*' => 'exists:technologies,id',
        ]);

        DB::transaction(function () use ($job, $validated) {
            $job->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'responsibilities' => $validated['responsibilities'] ?? null,
                'requirements' => $validated['requirements'] ?? null,
                'benefits' => $validated['benefits'] ?? null,
                'salary_min' => $validated['salary_min'] ?? null,
                'salary_max' => $validated['salary_max'] ?? null,
                'location' => $validated['location'] ?? null,
                'work_type' => $validated['work_type'],
                'experience_level' => $validated['experience_level'] ?? null,
                'application_deadline' => $validated['application_deadline'] ?? null,
                'status' => $validated['status'],
            ]);

            $categoryIds = $validated['category_ids'] ?? [];
            $job->categories()->sync($categoryIds);

            $technologyIds = $validated['technology_ids'] ?? [];
            $job->technologies()->sync($technologyIds);
        });

        $job->load(['categories', 'technologies', 'employerProfile']);

        return response()->json([
            'message' => 'Job updated successfully.',
            'job' => new JobResource($job)
        ]);
    }

    /**
     * Remove the specified job.
     */
    public function destroy(Request $request, Job $job): JsonResponse
    {
        $user = $request->user();
        if ($job->employer_profile_id !== $user->employerProfile?->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $job->delete();

        return response()->json(['message' => 'Job deleted successfully.']);
    }

    /**
     * Get categories and technologies list.
     */
    public function metaOptions(): JsonResponse
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $technologies = Technology::select('id', 'name')->orderBy('name')->get();

        return response()->json([
            'categories' => $categories,
            'technologies' => $technologies
        ]);
    }
}

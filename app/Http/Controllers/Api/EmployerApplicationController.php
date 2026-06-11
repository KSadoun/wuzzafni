<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployerApplicationResource;
use App\Http\Resources\JobResource;
use App\Models\Application;
use App\Models\Job;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployerApplicationController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function jobs(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'employer' || ! $user->employerProfile) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $jobs = Job::with(['categories', 'technologies'])
            ->where('employer_profile_id', $user->employerProfile->id)
            ->latest()
            ->paginate(15);

        return response()->json(
            JobResource::collection($jobs)->response()->getData(true)
        );
    }

    public function index(Request $request, Job $job): JsonResponse
    {
        $request->user()->load('employerProfile');
        $this->authorize('manageApplications', $job);

        $applications = Application::with(['candidateProfile.user', 'payment'])
            ->where('job_id', $job->id)
            ->latest('applied_at')
            ->paginate(15);

        return response()->json(
            EmployerApplicationResource::collection($applications)->response()->getData(true)
        );
    }

    public function accept(Request $request, Application $application): JsonResponse
    {
        $application->load('job');
        $this->authorize('manageApplications', $application->job);

        if ($application->status === 'accepted') {
            $payment = $application->payment ?? $this->paymentService->createPendingPayment($application);

            return response()->json([
                'message' => 'Candidate already accepted.',
                'application' => new EmployerApplicationResource($application->load(['candidateProfile.user', 'payment'])),
                'payment' => $payment,
            ]);
        }

        if (! in_array($application->status, ['pending', 'reviewed'])) {
            return response()->json(['message' => 'Only pending or reviewed applications can be accepted.'], 422);
        }

        $application->update(['status' => 'accepted']);
        $payment = $this->paymentService->createPendingPayment($application);

        return response()->json([
            'message' => 'Candidate accepted. Please complete payment to finalize.',
            'application' => new EmployerApplicationResource($application->load(['candidateProfile.user', 'payment'])),
            'payment' => $payment,
        ]);
    }

    public function showPayment(Request $request, Application $application): JsonResponse
    {
        $application->load(['job', 'candidateProfile.user', 'payment']);
        $this->authorize('view', $application);

        if ($application->status !== 'accepted') {
            return response()->json(['message' => 'Payment is only required for accepted applications.'], 422);
        }

        $payment = $application->payment ?? $this->paymentService->createPendingPayment($application);

        return response()->json([
            'application' => new EmployerApplicationResource($application),
            'payment' => $payment,
            'amount' => $payment->amount,
            'currency' => config('payment.currency', 'USD'),
        ]);
    }
}

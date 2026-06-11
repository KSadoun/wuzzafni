<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function getJobAnalytics(Request $request, Job $job): JsonResponse
    {
        $request->user()->load('employerProfile');
        $this->authorize('viewAnalytics', $job);

        return response()->json([
            'job' => [
                'id' => $job->id,
                'title' => $job->title,
            ],
            'total_applications' => $this->getTotalApplications($job),
            'applications_per_day' => $this->getApplicationsPerDay($job),
            'pending' => $this->getPendingApplications($job),
            'reviewed' => $this->getReviewedApplications($job),
            'accepted' => $this->getAcceptedApplications($job),
            'rejected' => $this->getRejectedApplications($job),
        ]);
    }

    public function getTotalApplications(Job $job): int
    {
        return Application::where('job_id', $job->id)->count();
    }

    public function getApplicationsPerDay(Job $job): float
    {
        $totalApplications = Application::where('job_id', $job->id)->count();
        $totalDays = $job->created_at->diffInDays(now()) + 1;

        return round($totalApplications / $totalDays, 2);
    }

    public function getPendingApplications(Job $job): int
    {
        return Application::where('job_id', $job->id)->where('status', 'pending')->count();
    }

    public function getReviewedApplications(Job $job): int
    {
        return Application::where('job_id', $job->id)->where('status', 'reviewed')->count();
    }

    public function getAcceptedApplications(Job $job): int
    {
        return Application::where('job_id', $job->id)->where('status', 'accepted')->count();
    }

    public function getRejectedApplications(Job $job): int
    {
        return Application::where('job_id', $job->id)->where('status', 'rejected')->count();
    }
}

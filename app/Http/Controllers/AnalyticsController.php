<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function getJobAnalytics(Request $request, Job $job)
    {
        return response()->json([
            'total_applications' => $this->getTotalApplications($job),
            'applications_per_day' => $this->getApplicationsPerDay($job),
            'pending' => $this->getPendingApplications($job),
            'reviewed' => $this->getReviewedApplications($job),
            'accepted' => $this->getAcceptedApplications($job),
            'rejected' => $this->getRejectedApplications($job),
        ]);
    }

    public function getTotalApplications(Job $job)
    {
        $applications_count = Application::where('job_id', $job->id)->count();

        return $applications_count;
    }

    public function getApplicationsPerDay(Job $job)
    {
        $total_applications = Application::where('job_id', $job->id)->count();
        $total_days = $job->created_at->diffInDays(now()) + 1; // +1 to avoid division by zero
        $applications_per_day = round($total_applications / $total_days, 2);

        return $applications_per_day;
    }

    public function getPendingApplications(Job $job)
    {
        $pending_count = Application::where('job_id', $job->id)->where('status', 'pending')->count();

        return $pending_count;
    }

    public function getReviewedApplications(Job $job)
    {
        $reviewed_count = Application::where('job_id', $job->id)->where('status', 'reviewed')->count();

        return $reviewed_count;
    }

    public function getAcceptedApplications(Job $job)
    {
        $accepted_count = Application::where('job_id', $job->id)->where('status', 'accepted')->count();

        return $accepted_count;
    }

    public function getRejectedApplications(Job $job)
    {
        $rejected_count = Application::where('job_id', $job->id)->where('status', 'rejected')->count();

        return $rejected_count;
    }
}

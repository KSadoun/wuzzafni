<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\JobComment;
use App\Models\Application;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    
    public function dashboard()
    {
        $stats = [
            'total_employers'    => User::whereHas('employerProfile')->count(),
            'total_candidates'   => User::whereHas('candidateProfile')->count(),
            'pending_jobs'       => Job::where('status', 'pending')->count(),
            'approved_jobs'      => Job::where('status', 'approved')->count(),
            'total_applications' => Application::count(),
            'total_job_views'    => Job::sum('views_count'),
        ];

        return Inertia::render('admin/Dashboard', [
            'stats' => $stats
        ]);
    }

  
    public function jobApprovals(Request $request)
    {
        $status = $request->query('status', 'pending');

        $jobs = Job::with(['employerProfile.user', 'categories', 'technologies'])
            ->where('status', $status)
            ->latest()
            ->get();

        return Inertia::render('admin/JobApprovals', [
            'jobs'          => $jobs,
            'currentStatus' => $status
        ]);
    }

  
    public function updateJobStatus(Request $request, Job $job)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $job->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', "Job status changed to {$request->status} successfully.");
    }

  
    public function commentModeration()
    {
        $comments = JobComment::with(['user', 'job'])
            ->latest()
            ->get();

        return Inertia::render('admin/CommentModeration', [
            'comments' => $comments
        ]);
    }


    public function destroyComment(JobComment $comment)
    {
        $comment->delete();

        return redirect()->back()->with('success', 'Comment removed from platform.');
    }
}
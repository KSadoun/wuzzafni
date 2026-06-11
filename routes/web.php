<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        if (auth()->user()->role === 'employer') {
            return redirect()->route('employer.jobs');
        }
        return Inertia::render('Dashboard');
    })->name('dashboard');
    
    // Candidate Routes
    Route::inertia('candidate/applications', 'candidate/ApplicationsView')->name('candidate.applications');

    // Employer Routes
    Route::middleware('role:employer')->group(function () {
        Route::inertia('employer/jobs', 'employer/EmployerJobsView')->name('employer.jobs');
        Route::inertia('employer/jobs/create', 'employer/JobFormView')->name('employer.jobs.create');
        Route::get('employer/jobs/{id}/edit', function ($id) {
            return Inertia::render('employer/JobFormView', ['jobId' => $id]);
        })->name('employer.jobs.edit');
    });
});

// Jobs Routes
Route::inertia('jobs', 'candidate/JobsView')->name('jobs.index');
Route::get('jobs/{id}', function ($id) {
    return Inertia::render('candidate/JobDetailsView', ['jobId' => $id]);
})->name('jobs.show');

require __DIR__.'/settings.php';

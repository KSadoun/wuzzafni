<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return redirect(auth()->user()->dashboardPath());
    })->name('dashboard');

    // Candidate Routes
    Route::inertia('candidate/applications', 'candidate/ApplicationsView')->name('candidate.applications');

    // Employer SPA (Vue Router)
    Route::get('/employer/{vue?}', function () {
        return Inertia::render('employer/EmployerShell');
    })->where('vue', '.*')->name('employer.shell');
});

// Jobs Routes
Route::inertia('jobs', 'candidate/JobsView')->name('jobs.index');
Route::get('jobs/{id}', function ($id) {
    return Inertia::render('candidate/JobDetailsView', ['jobId' => $id]);
})->name('jobs.show');

require __DIR__.'/settings.php';

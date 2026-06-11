<?php

use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    
    // Candidate Routes
    Route::inertia('candidate/applications', 'candidate/ApplicationsView')->name('candidate.applications');
});

// Jobs Routes
Route::inertia('jobs', 'candidate/JobsView')->name('jobs.index');
Route::get('jobs/{id}', function ($id) {
    return Inertia::render('candidate/JobDetailsView', ['jobId' => $id]);
})->name('jobs.show');

require __DIR__.'/settings.php';



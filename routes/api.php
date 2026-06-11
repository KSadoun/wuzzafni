<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CandidateProfileController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\ApplicationController;

Route::get('/health', function (Request $request) {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
})->name('health');

// Public Routes
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{job}', [JobController::class, 'show']);

// Protected Routes
Route::middleware('auth:web')->group(function () {
    // Candidate Profile
    Route::get('/candidate/profile', [CandidateProfileController::class, 'show']);

    // Applications
    Route::post('/jobs/{job}/apply', [ApplicationController::class, 'store']);
    Route::get('/candidate/applications', [ApplicationController::class, 'index']);
    Route::get('/candidate/applications/{application}', [ApplicationController::class, 'show']);
    Route::post('/candidate/applications/{application}/cancel', [ApplicationController::class, 'cancel']);
});

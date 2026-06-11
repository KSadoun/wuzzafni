<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\CandidateProfileController;
use App\Http\Controllers\Api\EmployerApplicationController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\PayPalController;
use App\Http\Controllers\Api\EmployerJobController;

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
    Route::get('/candidate/dashboard', [ApplicationController::class, 'dashboard']);
    Route::get('/candidate/applications', [ApplicationController::class, 'index']);
    Route::get('/candidate/applications/{application}', [ApplicationController::class, 'show']);
    Route::post('/candidate/applications/{application}/cancel', [ApplicationController::class, 'cancel']);

  
    // Analytics
    Route::get('/jobs/{job}/analytics', [AnalyticsController::class, 'getJobAnalytics'])->name('analytics');

    // PayPal
    Route::controller(PayPalController::class)->prefix('paypal')->group(function () {
        Route::get('payment', 'createPayment')->name('paypal.create');
        Route::get('success', 'success')->name('paypal.success');
        Route::get('cancel', 'cancel')->name('paypal.cancel');
    });
  
    // Employer Routes
    Route::middleware('role:employer')->group(function () {
        Route::get('/employer/jobs', [EmployerJobController::class, 'index']);
        Route::post('/employer/jobs', [EmployerJobController::class, 'store']);
        Route::get('/employer/jobs/{job}', [EmployerJobController::class, 'show']);
        Route::put('/employer/jobs/{job}', [EmployerJobController::class, 'update']);
        Route::delete('/employer/jobs/{job}', [EmployerJobController::class, 'destroy']);
    });

    // Metadata Routes
    Route::get('/meta/job-options', [EmployerJobController::class, 'metaOptions']);
});

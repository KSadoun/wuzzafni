<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        Route::get('/jobs', [AdminController::class, 'jobApprovals'])->name('jobs.index');
        Route::patch('/jobs/{job}/status', [AdminController::class, 'updateJobStatus'])->name('jobs.updateStatus');
        Route::get('/comments', [AdminController::class, 'commentModeration'])->name('comments.index');
        Route::delete('/comments/{comment}', [AdminController::class, 'destroyComment'])->name('comments.destroy');
    });

    });

require __DIR__.'/settings.php';


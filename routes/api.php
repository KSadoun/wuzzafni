<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayPalController;

Route::get('/health', function (Request $request) {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
})->name('health');

Route::get('/jobs/{job}/analytics', [AnalyticsController::class, 'getJobAnalytics'])->name('analytics');


// PayPal
Route::controller(PayPalController::class)->prefix('paypal')->group(function () {
    Route::get('payment', 'createPayment')->name('paypal.create');
    Route::get('success', 'success')->name('paypal.success');
    Route::get('cancel', 'cancel')->name('paypal.cancel');
});
<?php

use App\Http\Controllers\Auth\VerificationSuccessController;
use Illuminate\Support\Facades\Route;

// Email Verification Success Page
Route::get('/email/verify/success', [VerificationSuccessController::class, 'show'])
    ->name('verification.success');

// Fallback route for the root URL
Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to the API. Please use the API endpoints to interact with the application.',
    ]);
});

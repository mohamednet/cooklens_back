<?php

use App\Features\Auth\Controllers\AuthController;
use App\Features\Auth\Controllers\EmailVerificationController;
use App\Features\Auth\Controllers\PasswordResetController;
use App\Features\Auth\Controllers\ProfileController;
use App\Features\Auth\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // Email verification
    Route::prefix('email')->group(function () {
        Route::post('/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail']);
        Route::get('/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
            ->middleware('signed')
            ->name('verification.verify');
    });

    // Profile management
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::delete('/', [ProfileController::class, 'destroy']);
    });

    // Token management
    Route::prefix('tokens')->group(function () {
        Route::get('/', [TokenController::class, 'index']);
        Route::delete('/{tokenId}', [TokenController::class, 'destroy']);
        Route::delete('/', [TokenController::class, 'destroyAll']);
    });
});

<?php

use App\Features\Auth\Controllers\AuthController;
use App\Features\Auth\Controllers\EmailVerificationController;
use App\Features\Auth\Controllers\PasswordResetController;
use App\Features\Auth\Controllers\ProfileController;
use App\Features\Auth\Controllers\TokenController;
use App\Features\Recipes\Controllers\RecipeController;
use App\Features\Recipes\Controllers\RecipeIngredientController;
use App\Features\Recipes\Controllers\RecipeSearchController;
use App\Features\Recipes\Controllers\RecipeStepController;
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

    // My Recipes
    Route::get('/my-recipes', [RecipeController::class, 'myRecipes']);

    // Recipe Management
    Route::prefix('recipes')->group(function () {
        Route::post('/', [RecipeController::class, 'store']);
        Route::put('/{recipe}', [RecipeController::class, 'update']);
        Route::delete('/{recipe}', [RecipeController::class, 'destroy']);
        Route::post('/{recipe}/publish', [RecipeController::class, 'publish']);

        // Recipe Ingredients
        Route::post('/{recipe}/ingredients', [RecipeIngredientController::class, 'store']);
        Route::put('/{recipe}/ingredients/{ingredientId}', [RecipeIngredientController::class, 'update']);
        Route::delete('/{recipe}/ingredients/{ingredientId}', [RecipeIngredientController::class, 'destroy']);
        Route::post('/{recipe}/ingredients/sync', [RecipeIngredientController::class, 'sync']);

        // Recipe Steps
        Route::post('/{recipe}/steps', [RecipeStepController::class, 'store']);
        Route::put('/{recipe}/steps/{step}', [RecipeStepController::class, 'update']);
        Route::delete('/{recipe}/steps/{step}', [RecipeStepController::class, 'destroy']);
        Route::post('/{recipe}/steps/reorder', [RecipeStepController::class, 'reorder']);
    });
});

// Public Recipe Routes
Route::prefix('recipes')->group(function () {
    Route::get('/', [RecipeController::class, 'index']);
    Route::get('/search', [RecipeSearchController::class, 'search']);
    Route::post('/search/ingredients', [RecipeSearchController::class, 'searchByIngredients']);
    Route::get('/{slug}', [RecipeController::class, 'show']);
});

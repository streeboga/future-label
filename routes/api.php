<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ForgotPasswordController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\LogoutController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\RegisterController;
use App\Http\Controllers\Api\V1\ResetPasswordController;
use App\Http\Controllers\Api\V1\VerifyEmailController;
use App\Http\Middleware\EnsureUserHasAdminAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    // Auth (public)
    Route::post('auth/register', RegisterController::class)->name('auth.register');
    Route::post('auth/login', LoginController::class)->name('auth.login');
    Route::post('auth/forgot-password', ForgotPasswordController::class)->name('auth.forgot-password');
    Route::post('auth/reset-password', ResetPasswordController::class)->name('auth.reset-password');

    // Email verification (signed URL, public)
    Route::get('auth/email/verify/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed'])
        ->name('verification.verify');

    // Auth (protected)
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('auth/logout', LogoutController::class)->name('auth.logout');

        // Current user (for role-based interface routing)
        Route::get('me', MeController::class)->name('me');

        // Profile
        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('profile/{key}', [ProfileController::class, 'show'])->name('profile.show.other');
        Route::patch('profile/{key}', [ProfileController::class, 'update'])->name('profile.update.other');

        // Admin panel routes (admin + manager only)
        Route::prefix('admin')->middleware(EnsureUserHasAdminAccess::class)->group(function (): void {
            Route::get('dashboard', fn (): JsonResponse => response()->json([
                'data' => [
                    'type' => 'dashboard',
                    'attributes' => [
                        'message' => 'Admin dashboard',
                    ],
                ],
            ]))->name('admin.dashboard');
        });
    });
});

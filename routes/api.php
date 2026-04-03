<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ForgotPasswordController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\LogoutController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\RegisterController;
use App\Http\Controllers\Api\V1\ReleaseController;
use App\Http\Controllers\Api\V1\ResetPasswordController;
use App\Http\Controllers\Api\V1\ServiceCatalogController;
use App\Http\Controllers\Api\V1\TrackController;
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

    // Service Catalog (public - list active services)
    Route::get('services', [ServiceCatalogController::class, 'index'])->name('services.index');
    Route::get('services/{service}', [ServiceCatalogController::class, 'show'])->name('services.show');

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

        // Orders (authenticated users)
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');

        // Releases
        Route::get('releases', [ReleaseController::class, 'index'])->name('releases.index');
        Route::post('releases', [ReleaseController::class, 'store'])->name('releases.store');
        Route::get('releases/{release}', [ReleaseController::class, 'show'])->name('releases.show');
        Route::patch('releases/{release}', [ReleaseController::class, 'update'])->name('releases.update');
        Route::delete('releases/{release}', [ReleaseController::class, 'destroy'])->name('releases.destroy');
        Route::post('releases/{release}/submit', [ReleaseController::class, 'submit'])->name('releases.submit');

        // Tracks (nested under releases)
        Route::get('releases/{release}/tracks', [TrackController::class, 'index'])->name('releases.tracks.index');
        Route::post('releases/{release}/tracks', [TrackController::class, 'store'])->name('releases.tracks.store');
        Route::delete('releases/{release}/tracks/{track}', [TrackController::class, 'destroy'])->name('releases.tracks.destroy');

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

            // Service Catalog management (admin only)
            Route::post('services', [ServiceCatalogController::class, 'store'])->name('admin.services.store');
            Route::patch('services/{service}', [ServiceCatalogController::class, 'update'])->name('admin.services.update');
            Route::delete('services/{service}', [ServiceCatalogController::class, 'destroy'])->name('admin.services.destroy');
        });
    });
});

<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AdminDashboardController;
use App\Http\Controllers\Api\V1\AdminPaymentController;
use App\Http\Controllers\Api\V1\AdminReleaseController;
use App\Http\Controllers\Api\V1\AdminUserController;
use App\Http\Controllers\Api\V1\ContractController;
use App\Http\Controllers\Api\V1\ForgotPasswordController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\LogoutController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PaymentWebhookController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\RegisterController;
use App\Http\Controllers\Api\V1\ReleaseController;
use App\Http\Controllers\Api\V1\ResetPasswordController;
use App\Http\Controllers\Api\V1\ServiceCatalogController;
use App\Http\Controllers\Api\V1\TrackController;
use App\Http\Controllers\Api\V1\VerifyEmailController;
use App\Http\Middleware\EnsureUserHasAdminAccess;
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

    // Payment webhook (public - no auth)
    Route::post('payments/webhook', PaymentWebhookController::class)->name('payments.webhook');

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

        // Payments
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('releases/{release}/pay', [PaymentController::class, 'store'])->name('releases.payments.store');

        // Contracts
        Route::get('contracts', [ContractController::class, 'index'])->name('contracts.index');
        Route::post('releases/{release}/contract', [ContractController::class, 'store'])->name('releases.contracts.store');
        Route::patch('contracts/{contract}/accept', [ContractController::class, 'accept'])->name('contracts.accept');
        Route::get('contracts/{contract}/pdf', [ContractController::class, 'downloadPdf'])->name('contracts.pdf');

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

        // Admin panel routes (admin + manager only)
        Route::prefix('admin')->middleware(EnsureUserHasAdminAccess::class)->group(function (): void {
            Route::get('dashboard', AdminDashboardController::class)->name('admin.dashboard');

            // Release management (admin + manager)
            Route::get('releases', [AdminReleaseController::class, 'index'])->name('admin.releases.index');
            Route::get('releases/{release}', [AdminReleaseController::class, 'show'])->name('admin.releases.show');
            Route::patch('releases/{release}/status', [AdminReleaseController::class, 'updateStatus'])->name('admin.releases.status');

            // User management (admin + manager)
            Route::get('users', [AdminUserController::class, 'index'])->name('admin.users.index');
            Route::get('users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');

            // Payment management (admin + manager)
            Route::patch('payments/{payment}/confirm', [AdminPaymentController::class, 'confirm'])->name('admin.payments.confirm');
            Route::patch('payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('admin.payments.reject');

            // Service Catalog management (admin only)
            Route::post('services', [ServiceCatalogController::class, 'store'])->name('admin.services.store');
            Route::patch('services/{service}', [ServiceCatalogController::class, 'update'])->name('admin.services.update');
            Route::delete('services/{service}', [ServiceCatalogController::class, 'destroy'])->name('admin.services.destroy');
        });
    });
});

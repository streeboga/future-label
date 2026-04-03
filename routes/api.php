<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\RegisterController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    // Auth (public)
    Route::post('auth/register', RegisterController::class)->name('auth.register');

    // Email verification stub (Story 1.2 will implement full flow)
    Route::get('auth/email/verify/{id}/{hash}', fn () => response()->noContent())
        ->middleware(['signed'])
        ->name('verification.verify');
});

<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ForgotPasswordController;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

covers(ForgotPasswordController::class);

// --- Happy Path ---

it('sends a password reset link to a valid email', function (): void {
    Notification::fake();

    $user = User::factory()->create(['email' => 'artist@example.com']);

    $response = $this->postJson('/api/v1/auth/forgot-password', [
        'email' => 'artist@example.com',
    ]);

    $response->assertOk();
    $response->assertJsonStructure([
        'meta' => ['message'],
    ]);
    $response->assertJsonPath('meta.message', 'If the email exists, a reset link has been sent.');

    Notification::assertSentTo($user, ResetPassword::class);
});

it('returns 200 even for non-existent email (no information leak)', function (): void {
    Notification::fake();

    $response = $this->postJson('/api/v1/auth/forgot-password', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertOk();
    $response->assertJsonPath('meta.message', 'If the email exists, a reset link has been sent.');

    Notification::assertNothingSent();
});

// --- Validation Errors ---

it('returns 422 when email is missing', function (): void {
    $response = $this->postJson('/api/v1/auth/forgot-password', []);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('email');
});

it('returns 422 when email is invalid format', function (): void {
    $response = $this->postJson('/api/v1/auth/forgot-password', [
        'email' => 'not-an-email',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('email');
});

// --- Edge Cases ---

it('is case-insensitive for email', function (): void {
    Notification::fake();

    $user = User::factory()->create(['email' => 'artist@example.com']);

    $response = $this->postJson('/api/v1/auth/forgot-password', [
        'email' => 'ARTIST@Example.COM',
    ]);

    $response->assertOk();

    Notification::assertSentTo($user, ResetPassword::class);
});

it('rate limits password reset requests', function (): void {
    Notification::fake();

    User::factory()->create(['email' => 'artist@example.com']);

    // First request should succeed
    $this->postJson('/api/v1/auth/forgot-password', [
        'email' => 'artist@example.com',
    ])->assertOk();

    // Second immediate request should be throttled
    $response = $this->postJson('/api/v1/auth/forgot-password', [
        'email' => 'artist@example.com',
    ]);

    // Laravel's password broker throttles to once per minute
    $response->assertUnprocessable();
});

<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ResetPasswordController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

covers(ResetPasswordController::class);

// --- Happy Path ---

it('resets password with a valid token', function (): void {
    $user = User::factory()->create(['email' => 'artist@example.com']);

    $token = Password::createToken($user);

    $response = $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'artist@example.com',
        'token' => $token,
        'password' => 'NewSecureP@ss1',
        'password_confirmation' => 'NewSecureP@ss1',
    ]);

    $response->assertOk();
    $response->assertJsonStructure([
        'meta' => ['message'],
    ]);
    $response->assertJsonPath('meta.message', 'Password has been reset successfully.');

    // Verify the password was actually changed
    $user->refresh();
    expect(Hash::check('NewSecureP@ss1', $user->password))->toBeTrue();
});

it('allows login with new password after reset', function (): void {
    $user = User::factory()->create([
        'email' => 'artist@example.com',
        'password' => 'OldP@ssword1',
    ]);

    $token = Password::createToken($user);

    $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'artist@example.com',
        'token' => $token,
        'password' => 'NewSecureP@ss1',
        'password_confirmation' => 'NewSecureP@ss1',
    ])->assertOk();

    // Login with new password
    $this->postJson('/api/v1/auth/login', [
        'email' => 'artist@example.com',
        'password' => 'NewSecureP@ss1',
    ])->assertOk();

    // Old password should no longer work
    $this->postJson('/api/v1/auth/login', [
        'email' => 'artist@example.com',
        'password' => 'OldP@ssword1',
    ])->assertUnauthorized();
});

// --- Invalid Token ---

it('returns 422 when token is invalid', function (): void {
    User::factory()->create(['email' => 'artist@example.com']);

    $response = $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'artist@example.com',
        'token' => 'invalid-token',
        'password' => 'NewSecureP@ss1',
        'password_confirmation' => 'NewSecureP@ss1',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('email');
});

it('returns 422 when token is expired', function (): void {
    $user = User::factory()->create(['email' => 'artist@example.com']);

    $token = Password::createToken($user);

    // Simulate token expiration by traveling forward in time
    $this->travel(61)->minutes();

    $response = $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'artist@example.com',
        'token' => $token,
        'password' => 'NewSecureP@ss1',
        'password_confirmation' => 'NewSecureP@ss1',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('email');
});

// --- Validation Errors ---

it('returns 422 when email is missing', function (): void {
    $response = $this->postJson('/api/v1/auth/reset-password', [
        'token' => 'some-token',
        'password' => 'NewSecureP@ss1',
        'password_confirmation' => 'NewSecureP@ss1',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('email');
});

it('returns 422 when token is missing', function (): void {
    $response = $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'artist@example.com',
        'password' => 'NewSecureP@ss1',
        'password_confirmation' => 'NewSecureP@ss1',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('token');
});

it('returns 422 when password is missing', function (): void {
    $response = $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'artist@example.com',
        'token' => 'some-token',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('password');
});

it('returns 422 when password confirmation does not match', function (): void {
    $response = $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'artist@example.com',
        'token' => 'some-token',
        'password' => 'NewSecureP@ss1',
        'password_confirmation' => 'DifferentP@ss1',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('password');
});

it('returns 422 when password is too short', function (): void {
    $response = $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'artist@example.com',
        'token' => 'some-token',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('password');
});

it('returns 422 when body is empty', function (): void {
    $response = $this->postJson('/api/v1/auth/reset-password', []);

    $response->assertUnprocessable();
});

// --- Edge Cases ---

it('is case-insensitive for email during reset', function (): void {
    $user = User::factory()->create(['email' => 'artist@example.com']);

    $token = Password::createToken($user);

    $response = $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'ARTIST@Example.COM',
        'token' => $token,
        'password' => 'NewSecureP@ss1',
        'password_confirmation' => 'NewSecureP@ss1',
    ]);

    $response->assertOk();

    $user->refresh();
    expect(Hash::check('NewSecureP@ss1', $user->password))->toBeTrue();
});

it('invalidates token after successful reset', function (): void {
    $user = User::factory()->create(['email' => 'artist@example.com']);

    $token = Password::createToken($user);

    // First reset should succeed
    $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'artist@example.com',
        'token' => $token,
        'password' => 'NewSecureP@ss1',
        'password_confirmation' => 'NewSecureP@ss1',
    ])->assertOk();

    // Same token should not work again
    $response = $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'artist@example.com',
        'token' => $token,
        'password' => 'AnotherP@ss1',
        'password_confirmation' => 'AnotherP@ss1',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('email');
});

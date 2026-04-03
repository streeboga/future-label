<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\LogoutController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

covers(LogoutController::class);

// --- Happy Path ---

it('logs out and returns 204 no content', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withToken($token)
        ->postJson('/api/v1/auth/logout');

    $response->assertNoContent();
});

it('revokes only the current token on logout', function (): void {
    $user = User::factory()->create();
    $token1 = $user->createToken('auth_token')->plainTextToken;
    $user->createToken('auth_token')->plainTextToken;

    expect($user->tokens()->count())->toBe(2);

    // Logout with token1
    $this->withToken($token1)
        ->postJson('/api/v1/auth/logout')
        ->assertNoContent();

    // Only one token should remain (the second one)
    expect($user->tokens()->count())->toBe(1);
});

// --- Unauthenticated ---

it('returns 401 when not authenticated', function (): void {
    $response = $this->postJson('/api/v1/auth/logout');

    $response->assertUnauthorized();
});

it('returns 401 when token is invalid', function (): void {
    $response = $this->withToken('invalid-token-value')
        ->postJson('/api/v1/auth/logout');

    $response->assertUnauthorized();
});

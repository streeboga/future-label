<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\LoginController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

covers(LoginController::class);

// --- Happy Path ---

it('logs in a verified user and returns a bearer token', function (): void {
    $user = User::factory()->create([
        'email' => 'artist@example.com',
        'password' => 'SecureP@ss1',
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'artist@example.com',
        'password' => 'SecureP@ss1',
    ]);

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'type',
            'id',
            'attributes' => ['name', 'email', 'role', 'created_at', 'updated_at'],
            'links' => ['self'],
        ],
        'meta' => ['token'],
    ]);
    $response->assertJsonPath('data.type', 'users');
    $response->assertJsonPath('data.id', $user->key);
    $response->assertJsonPath('data.attributes.email', 'artist@example.com');
    expect($response->json('meta.token'))->toBeString()->not->toBeEmpty();
});

it('returns token that can be used for authenticated requests', function (): void {
    User::factory()->create([
        'email' => 'artist@example.com',
        'password' => 'SecureP@ss1',
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'artist@example.com',
        'password' => 'SecureP@ss1',
    ]);

    $token = $response->json('meta.token');

    // Use the token to access a protected route (logout as proxy)
    $this->withToken($token)
        ->postJson('/api/v1/auth/logout')
        ->assertNoContent();
});

// --- Wrong Credentials ---

it('returns 401 when password is incorrect', function (): void {
    User::factory()->create([
        'email' => 'artist@example.com',
        'password' => 'SecureP@ss1',
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'artist@example.com',
        'password' => 'WrongPassword1',
    ]);

    $response->assertUnauthorized();
    $response->assertJsonPath('message', 'The provided credentials are incorrect.');
});

it('returns 401 when email does not exist', function (): void {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'SecureP@ss1',
    ]);

    $response->assertUnauthorized();
});

// --- Validation Errors ---

it('returns 422 when email is missing', function (): void {
    $response = $this->postJson('/api/v1/auth/login', [
        'password' => 'SecureP@ss1',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('email');
});

it('returns 422 when password is missing', function (): void {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'artist@example.com',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('password');
});

it('returns 422 when email is invalid format', function (): void {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'not-an-email',
        'password' => 'SecureP@ss1',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('email');
});

it('returns 422 when body is empty', function (): void {
    $response = $this->postJson('/api/v1/auth/login', []);

    $response->assertUnprocessable();
});

// --- Edge Cases ---

it('is case-insensitive for email', function (): void {
    User::factory()->create([
        'email' => 'artist@example.com',
        'password' => 'SecureP@ss1',
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'ARTIST@Example.COM',
        'password' => 'SecureP@ss1',
    ]);

    $response->assertOk();
    expect($response->json('meta.token'))->toBeString()->not->toBeEmpty();
});

it('allows login for unverified user', function (): void {
    User::factory()->unverified()->create([
        'email' => 'unverified@example.com',
        'password' => 'SecureP@ss1',
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'unverified@example.com',
        'password' => 'SecureP@ss1',
    ]);

    // Unverified users can still login — they'll see limited UI
    $response->assertOk();
    expect($response->json('meta.token'))->toBeString()->not->toBeEmpty();
});

it('creates a new token on each login', function (): void {
    User::factory()->create([
        'email' => 'artist@example.com',
        'password' => 'SecureP@ss1',
    ]);

    $response1 = $this->postJson('/api/v1/auth/login', [
        'email' => 'artist@example.com',
        'password' => 'SecureP@ss1',
    ]);

    $response2 = $this->postJson('/api/v1/auth/login', [
        'email' => 'artist@example.com',
        'password' => 'SecureP@ss1',
    ]);

    $token1 = $response1->json('meta.token');
    $token2 = $response2->json('meta.token');

    expect($token1)->not->toBe($token2);
});

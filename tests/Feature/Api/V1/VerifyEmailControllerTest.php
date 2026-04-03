<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\VerifyEmailController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

covers(VerifyEmailController::class);

// --- Happy Path ---

it('verifies email with a valid signed URL', function (): void {
    $user = User::factory()->unverified()->create();

    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $response = $this->getJson($url);

    $response->assertOk();
    $response->assertJsonPath('data.message', 'Email verified successfully.');

    $user->refresh();
    expect($user->email_verified_at)->not->toBeNull();
});

it('does not change email_verified_at if already verified', function (): void {
    $verifiedAt = now()->subDay();
    $user = User::factory()->create([
        'email_verified_at' => $verifiedAt,
    ]);

    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $response = $this->getJson($url);

    $response->assertOk();

    $user->refresh();
    // Already verified — timestamp should remain the same
    expect($user->email_verified_at->timestamp)->toBe($verifiedAt->timestamp);
});

// --- Invalid Signature ---

it('returns 403 when signature is invalid', function (): void {
    $user = User::factory()->unverified()->create();

    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    // Tamper with the signature
    $tamperedUrl = $url.'&tampered=1';

    $response = $this->getJson($tamperedUrl);

    $response->assertForbidden();
});

it('returns 403 when URL has expired', function (): void {
    $user = User::factory()->unverified()->create();

    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->subMinutes(1),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $response = $this->getJson($url);

    $response->assertForbidden();
});

// --- Invalid Hash ---

it('returns 403 when hash does not match user email', function (): void {
    $user = User::factory()->unverified()->create();

    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong@email.com')],
    );

    $response = $this->getJson($url);

    $response->assertForbidden();
    $response->assertJsonPath('message', 'Invalid verification link.');
});

// --- Invalid User ---

it('returns 404 when user does not exist', function (): void {
    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => 99999, 'hash' => sha1('fake@example.com')],
    );

    $response = $this->getJson($url);

    $response->assertNotFound();
});

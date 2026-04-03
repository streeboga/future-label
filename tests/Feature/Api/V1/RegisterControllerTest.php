<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Events\UserRegistered;
use App\Http\Controllers\Api\V1\RegisterController;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

covers(RegisterController::class);

// --- Happy Path ---

it('registers a new artist and returns 201 with JSON:API structure', function (): void {
    Event::fake([UserRegistered::class]);

    $payload = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'SecureP@ss1',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertCreated();
    $response->assertHeader('Location');
    $response->assertJsonStructure([
        'data' => [
            'type',
            'id',
            'attributes' => ['name', 'email', 'role', 'created_at', 'updated_at'],
            'links' => ['self'],
        ],
    ]);
    $response->assertJsonPath('data.type', 'users');
    $response->assertJsonPath('data.attributes.name', 'John Doe');
    $response->assertJsonPath('data.attributes.email', 'john@example.com');
    $response->assertJsonPath('data.attributes.role', 'artist');

    // id is public key (prefix + ULID)
    $id = $response->json('data.id');
    expect($id)->toStartWith('usr_');

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'role' => 'artist',
    ]);

    Event::assertDispatched(UserRegistered::class);
});

it('hashes the password with bcrypt cost >= 12', function (): void {
    Event::fake([UserRegistered::class]);

    $payload = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'SecureP@ss1',
    ];

    $this->postJson('/api/v1/auth/register', $payload)->assertCreated();

    $user = User::where('email', 'jane@example.com')->first();
    expect($user)->not->toBeNull();

    // Verify password is hashed and correct
    expect(Hash::check('SecureP@ss1', $user->password))->toBeTrue();

    // Verify bcrypt hash format ($2y$12$ means cost=12)
    expect($user->password)->toMatch('/^\$2[aby]\$1[2-9]\$/');
});

it('sends email verification notification on registration', function (): void {
    Notification::fake();
    Event::fake([UserRegistered::class]);

    $payload = [
        'name' => 'Verify Me',
        'email' => 'verify@example.com',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'SecureP@ss1',
    ];

    $this->postJson('/api/v1/auth/register', $payload)->assertCreated();

    $user = User::where('email', 'verify@example.com')->first();
    Notification::assertSentTo($user, VerifyEmail::class);
});

it('assigns artist role to newly registered user', function (): void {
    Event::fake([UserRegistered::class]);

    $payload = [
        'name' => 'Artist User',
        'email' => 'artist@example.com',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'SecureP@ss1',
    ];

    $this->postJson('/api/v1/auth/register', $payload)->assertCreated();

    $user = User::where('email', 'artist@example.com')->first();
    expect($user->role)->toBe(UserRole::Artist);
});

it('returns a bearer token in the response', function (): void {
    Event::fake([UserRegistered::class]);

    $payload = [
        'name' => 'Token User',
        'email' => 'token@example.com',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'SecureP@ss1',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertCreated();
    $response->assertJsonStructure([
        'meta' => ['token'],
    ]);
    expect($response->json('meta.token'))->toBeString()->not->toBeEmpty();
});

// --- Validation Errors ---

it('returns 422 when name is missing', function (): void {
    $payload = [
        'email' => 'john@example.com',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'SecureP@ss1',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('name');
});

it('returns 422 when email is missing', function (): void {
    $payload = [
        'name' => 'John Doe',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'SecureP@ss1',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('email');
});

it('returns 422 when email is invalid', function (): void {
    $payload = [
        'name' => 'John Doe',
        'email' => 'not-an-email',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'SecureP@ss1',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('email');
});

it('returns 422 when password is missing', function (): void {
    $payload = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('password');
});

it('returns 422 when password confirmation does not match', function (): void {
    $payload = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'DifferentPass1',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('password');
});

it('returns 422 when password is too short', function (): void {
    $payload = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'Short1',
        'password_confirmation' => 'Short1',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('password');
});

it('returns 422 when name exceeds max length', function (): void {
    $payload = [
        'name' => str_repeat('a', 256),
        'email' => 'john@example.com',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'SecureP@ss1',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('name');
});

it('returns 422 when body is empty', function (): void {
    $response = $this->postJson('/api/v1/auth/register', []);

    $response->assertUnprocessable();
});

// --- Duplicate Email ---

it('returns 422 when email is already taken', function (): void {
    User::factory()->create(['email' => 'taken@example.com']);

    $payload = [
        'name' => 'John Doe',
        'email' => 'taken@example.com',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'SecureP@ss1',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('email');
});

// --- Edge Cases ---

it('strips HTML tags from name', function (): void {
    Event::fake([UserRegistered::class]);

    $payload = [
        'name' => '<script>alert("xss")</script>John',
        'email' => 'xss@example.com',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'SecureP@ss1',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.name', 'alert("xss")John');
});

it('accepts unicode characters in name', function (): void {
    Event::fake([UserRegistered::class]);

    $payload = [
        'name' => 'Артист Тест',
        'email' => 'unicode@example.com',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'SecureP@ss1',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.name', 'Артист Тест');
});

it('trims whitespace from name and email', function (): void {
    Event::fake([UserRegistered::class]);

    $payload = [
        'name' => '  John Doe  ',
        'email' => '  john.trim@example.com  ',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'SecureP@ss1',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.name', 'John Doe');
    $response->assertJsonPath('data.attributes.email', 'john.trim@example.com');
});

it('lowercases email during registration', function (): void {
    Event::fake([UserRegistered::class]);

    $payload = [
        'name' => 'Case Test',
        'email' => 'John.DOE@Example.COM',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'SecureP@ss1',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.email', 'john.doe@example.com');
});

it('generates a unique key with usr_ prefix', function (): void {
    Event::fake([UserRegistered::class]);

    $payload = [
        'name' => 'Key Test',
        'email' => 'key@example.com',
        'password' => 'SecureP@ss1',
        'password_confirmation' => 'SecureP@ss1',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response->assertCreated();

    $key = $response->json('data.id');
    expect($key)->toStartWith('usr_');
    expect(strlen($key))->toBeLessThanOrEqual(40);

    $user = User::where('email', 'key@example.com')->first();
    expect($user->key)->toBe($key);
});

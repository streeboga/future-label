<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\MeController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

covers(MeController::class);

// --- GET /api/v1/me ---

it('returns current user with role=artist in JSON:API format', function (): void {
    $user = User::factory()->artist()->create([
        'name' => 'Артист Тестов',
        'email' => 'artist@example.com',
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/me');

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'type',
            'id',
            'attributes' => [
                'name',
                'email',
                'role',
            ],
            'links' => ['self'],
        ],
    ]);
    $response->assertJsonPath('data.type', 'users');
    $response->assertJsonPath('data.id', $user->key);
    $response->assertJsonPath('data.attributes.role', 'artist');
    $response->assertJsonPath('data.attributes.name', 'Артист Тестов');
    $response->assertJsonPath('data.attributes.email', 'artist@example.com');
});

it('returns current user with role=manager', function (): void {
    $user = User::factory()->manager()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/me');

    $response->assertOk();
    $response->assertJsonPath('data.attributes.role', 'manager');
});

it('returns current user with role=admin', function (): void {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/me');

    $response->assertOk();
    $response->assertJsonPath('data.attributes.role', 'admin');
});

it('returns 401 for unauthenticated /me request', function (): void {
    $response = $this->getJson('/api/v1/me');

    $response->assertUnauthorized();
});

<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AdminUserController;
use App\Models\Release;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

covers(AdminUserController::class);

// --- GET /api/v1/admin/users (Index) ---

it('lists all users for admin', function (): void {
    $admin = User::factory()->admin()->create();
    User::factory()->artist()->count(3)->create();

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/users');

    $response->assertOk();
    // 3 artists + 1 admin = 4 users
    $response->assertJsonCount(4, 'data');
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'type',
                'id',
                'attributes' => ['name', 'email', 'role'],
            ],
        ],
    ]);
});

it('lists all users for manager', function (): void {
    $manager = User::factory()->manager()->create();
    User::factory()->artist()->count(2)->create();

    $response = $this->actingAs($manager)->getJson('/api/v1/admin/users');

    $response->assertOk();
    $response->assertJsonCount(3, 'data');
});

it('forbids artist from listing admin users', function (): void {
    $artist = User::factory()->artist()->create();

    $response = $this->actingAs($artist)->getJson('/api/v1/admin/users');

    $response->assertForbidden();
});

it('returns 401 for unauthenticated admin users request', function (): void {
    $response = $this->getJson('/api/v1/admin/users');

    $response->assertUnauthorized();
});

it('searches users by name', function (): void {
    $admin = User::factory()->admin()->create(['name' => 'Admin Boss']);
    User::factory()->artist()->create(['name' => 'John Unique Artist']);
    User::factory()->artist()->create(['name' => 'Jane Doe']);

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/users?search=Unique');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.name', 'John Unique Artist');
});

it('searches users by email', function (): void {
    $admin = User::factory()->admin()->create();
    User::factory()->artist()->create(['email' => 'searchable@example.com']);
    User::factory()->artist()->create(['email' => 'other@example.com']);

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/users?search=searchable');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
});

// --- GET /api/v1/admin/users/{user} (Show) ---

it('shows user detail for admin', function (): void {
    $admin = User::factory()->admin()->create();
    $artist = User::factory()->artist()->create();

    $response = $this->actingAs($admin)->getJson("/api/v1/admin/users/{$artist->key}");

    $response->assertOk();
    $response->assertJsonPath('data.type', 'users');
    $response->assertJsonPath('data.id', $artist->key);
    $response->assertJsonStructure([
        'data' => [
            'type',
            'id',
            'attributes' => ['name', 'email', 'role'],
        ],
    ]);
});

it('shows user with releases included', function (): void {
    $admin = User::factory()->admin()->create();
    $artist = User::factory()->artist()->create();
    Release::factory()->count(2)->create(['user_id' => $artist->id]);

    $response = $this->actingAs($admin)->getJson("/api/v1/admin/users/{$artist->key}");

    $response->assertOk();
    $response->assertJsonPath('data.type', 'users');
});

it('returns 404 for non-existent user in admin', function (): void {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/users/usr_nonexistent');

    $response->assertNotFound();
});

it('forbids artist from viewing admin user detail', function (): void {
    $artist = User::factory()->artist()->create();
    $otherUser = User::factory()->artist()->create();

    $response = $this->actingAs($artist)->getJson("/api/v1/admin/users/{$otherUser->key}");

    $response->assertForbidden();
});

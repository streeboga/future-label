<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureUserHasAdminAccess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

covers(EnsureUserHasAdminAccess::class);

// --- Admin route access control ---

it('allows admin to access admin routes', function (): void {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/dashboard');

    $response->assertOk();
});

it('allows manager to access admin routes', function (): void {
    $manager = User::factory()->manager()->create();

    $response = $this->actingAs($manager)->getJson('/api/v1/admin/dashboard');

    $response->assertOk();
});

it('forbids artist from accessing admin routes', function (): void {
    $artist = User::factory()->artist()->create();

    $response = $this->actingAs($artist)->getJson('/api/v1/admin/dashboard');

    $response->assertForbidden();
});

it('returns 401 for unauthenticated admin route request', function (): void {
    $response = $this->getJson('/api/v1/admin/dashboard');

    $response->assertUnauthorized();
});

it('returns JSON:API error structure on 403', function (): void {
    $artist = User::factory()->artist()->create();

    $response = $this->actingAs($artist)->getJson('/api/v1/admin/dashboard');

    $response->assertForbidden();
    $response->assertJson([
        'message' => 'Access denied. Admin or manager role required.',
    ]);
});

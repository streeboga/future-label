<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AdminReleaseController;
use App\Models\Release;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

covers(AdminReleaseController::class);

// --- GET /api/v1/admin/releases (Index) ---

it('lists all releases for admin', function (): void {
    $admin = User::factory()->admin()->create();
    Release::factory()->count(3)->create();

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/releases');

    $response->assertOk();
    $response->assertJsonCount(3, 'data');
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'type',
                'id',
                'attributes' => ['title', 'status'],
            ],
        ],
    ]);
});

it('lists all releases for manager', function (): void {
    $manager = User::factory()->manager()->create();
    Release::factory()->count(2)->create();

    $response = $this->actingAs($manager)->getJson('/api/v1/admin/releases');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('forbids artist from listing admin releases', function (): void {
    $artist = User::factory()->artist()->create();

    $response = $this->actingAs($artist)->getJson('/api/v1/admin/releases');

    $response->assertForbidden();
});

it('returns 401 for unauthenticated admin releases request', function (): void {
    $response = $this->getJson('/api/v1/admin/releases');

    $response->assertUnauthorized();
});

it('filters admin releases by status', function (): void {
    $admin = User::factory()->admin()->create();
    Release::factory()->draft()->count(2)->create();
    Release::factory()->inReview()->count(1)->create();

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/releases?filter[status]=in_review');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.status', 'in_review');
});

it('searches admin releases by title', function (): void {
    $admin = User::factory()->admin()->create();
    Release::factory()->create(['title' => 'Unique Alpha Song']);
    Release::factory()->create(['title' => 'Another Release']);

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/releases?search=Alpha');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.title', 'Unique Alpha Song');
});

// --- GET /api/v1/admin/releases/{release} (Show) ---

it('shows release detail with tracks for admin', function (): void {
    $admin = User::factory()->admin()->create();
    $release = Release::factory()->create();

    $response = $this->actingAs($admin)->getJson("/api/v1/admin/releases/{$release->key}");

    $response->assertOk();
    $response->assertJsonPath('data.type', 'releases');
    $response->assertJsonPath('data.id', $release->key);
    $response->assertJsonStructure([
        'data' => [
            'type',
            'id',
            'attributes' => ['title', 'status'],
        ],
    ]);
});

it('returns 404 for non-existent release in admin', function (): void {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/releases/rel_nonexistent');

    $response->assertNotFound();
});

// --- PATCH /api/v1/admin/releases/{release}/status ---

it('approves a release in review', function (): void {
    $admin = User::factory()->admin()->create();
    $release = Release::factory()->inReview()->create();

    $response = $this->actingAs($admin)->patchJson("/api/v1/admin/releases/{$release->key}/status", [
        'action' => 'approve',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'approved');

    $this->assertDatabaseHas('releases', [
        'id' => $release->id,
        'status' => 'approved',
    ]);
});

it('rejects a release in review with comment', function (): void {
    $admin = User::factory()->admin()->create();
    $release = Release::factory()->inReview()->create();

    $response = $this->actingAs($admin)->patchJson("/api/v1/admin/releases/{$release->key}/status", [
        'action' => 'reject',
        'comment' => 'Low audio quality',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'rejected');
    $response->assertJsonPath('data.attributes.reject_reason', 'Low audio quality');

    $this->assertDatabaseHas('releases', [
        'id' => $release->id,
        'status' => 'rejected',
        'reject_reason' => 'Low audio quality',
    ]);
});

it('publishes an approved release', function (): void {
    $admin = User::factory()->admin()->create();
    $release = Release::factory()->approved()->create();

    $response = $this->actingAs($admin)->patchJson("/api/v1/admin/releases/{$release->key}/status", [
        'action' => 'publish',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'published');

    $release->refresh();
    expect($release->published_at)->not->toBeNull();
});

it('returns 422 when approving a non-in-review release', function (): void {
    $admin = User::factory()->admin()->create();
    $release = Release::factory()->draft()->create();

    $response = $this->actingAs($admin)->patchJson("/api/v1/admin/releases/{$release->key}/status", [
        'action' => 'approve',
    ]);

    $response->assertUnprocessable();
});

it('returns 422 when publishing a non-approved release', function (): void {
    $admin = User::factory()->admin()->create();
    $release = Release::factory()->inReview()->create();

    $response = $this->actingAs($admin)->patchJson("/api/v1/admin/releases/{$release->key}/status", [
        'action' => 'publish',
    ]);

    $response->assertUnprocessable();
});

it('returns 422 when action is missing', function (): void {
    $admin = User::factory()->admin()->create();
    $release = Release::factory()->inReview()->create();

    $response = $this->actingAs($admin)->patchJson("/api/v1/admin/releases/{$release->key}/status", []);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('action');
});

it('returns 422 when action is invalid', function (): void {
    $admin = User::factory()->admin()->create();
    $release = Release::factory()->inReview()->create();

    $response = $this->actingAs($admin)->patchJson("/api/v1/admin/releases/{$release->key}/status", [
        'action' => 'invalid_action',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('action');
});

it('forbids artist from changing release status', function (): void {
    $artist = User::factory()->artist()->create();
    $release = Release::factory()->inReview()->create();

    $response = $this->actingAs($artist)->patchJson("/api/v1/admin/releases/{$release->key}/status", [
        'action' => 'approve',
    ]);

    $response->assertForbidden();
});

it('rejects with default reason when comment is not provided', function (): void {
    $admin = User::factory()->admin()->create();
    $release = Release::factory()->inReview()->create();

    $response = $this->actingAs($admin)->patchJson("/api/v1/admin/releases/{$release->key}/status", [
        'action' => 'reject',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.reject_reason', 'Rejected by admin.');
});

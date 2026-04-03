<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\NotificationController;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

covers(NotificationController::class);

// --- GET /api/v1/notifications (Index) ---

it('returns paginated notifications for authenticated user, newest first', function (): void {
    $user = User::factory()->artist()->create();

    $old = Notification::factory()->for($user)->create(['created_at' => now()->subDay()]);
    $new = Notification::factory()->for($user)->create(['created_at' => now()]);

    $response = $this->actingAs($user)->getJson('/api/v1/notifications');

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'type',
                'id',
                'attributes' => [
                    'type',
                    'title',
                    'body',
                    'read_at',
                    'data',
                    'created_at',
                    'updated_at',
                ],
                'links' => ['self'],
            ],
        ],
    ]);

    $response->assertJsonPath('data.0.type', 'notifications');
    // Newest first
    $response->assertJsonPath('data.0.id', $new->key);
    $response->assertJsonPath('data.1.id', $old->key);
});

it('returns only notifications belonging to the authenticated user', function (): void {
    $user = User::factory()->artist()->create();
    $otherUser = User::factory()->artist()->create();

    Notification::factory()->for($user)->create();
    Notification::factory()->for($otherUser)->create();

    $response = $this->actingAs($user)->getJson('/api/v1/notifications');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
});

it('returns 401 for unauthenticated notification list request', function (): void {
    $response = $this->getJson('/api/v1/notifications');

    $response->assertUnauthorized();
});

it('filters notifications by type', function (): void {
    $user = User::factory()->artist()->create();

    Notification::factory()->for($user)->releaseStatusChanged()->create();
    Notification::factory()->for($user)->paymentConfirmed()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/notifications?filter[type]=payment_confirmed');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.type', 'payment_confirmed');
});

it('filters notifications by unread status', function (): void {
    $user = User::factory()->artist()->create();

    Notification::factory()->for($user)->unread()->create();
    Notification::factory()->for($user)->read()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/notifications?filter[unread]=true');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.read_at', null);
});

it('respects per_page parameter', function (): void {
    $user = User::factory()->artist()->create();
    Notification::factory()->for($user)->count(5)->create();

    $response = $this->actingAs($user)->getJson('/api/v1/notifications?per_page=2');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('caps per_page at 100', function (): void {
    $user = User::factory()->artist()->create();
    Notification::factory()->for($user)->count(3)->create();

    $response = $this->actingAs($user)->getJson('/api/v1/notifications?per_page=500');

    $response->assertOk();
    // All 3 returned since less than 100
    $response->assertJsonCount(3, 'data');
});

it('returns empty list when user has no notifications', function (): void {
    $user = User::factory()->artist()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/notifications');

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});

// --- PATCH /api/v1/notifications/{notification}/read ---

it('marks a notification as read and returns updated resource', function (): void {
    $user = User::factory()->artist()->create();
    $notification = Notification::factory()->for($user)->unread()->create();

    $response = $this->actingAs($user)->patchJson("/api/v1/notifications/{$notification->key}/read");

    $response->assertOk();
    $response->assertJsonPath('data.type', 'notifications');
    $response->assertJsonPath('data.id', $notification->key);

    // read_at should now be set
    $responseData = $response->json('data.attributes.read_at');
    expect($responseData)->not->toBeNull();

    $this->assertDatabaseMissing('notifications', [
        'key' => $notification->key,
        'read_at' => null,
    ]);
});

it('returns 403 when marking another users notification as read', function (): void {
    $owner = User::factory()->artist()->create();
    $other = User::factory()->artist()->create();
    $notification = Notification::factory()->for($owner)->unread()->create();

    $response = $this->actingAs($other)->patchJson("/api/v1/notifications/{$notification->key}/read");

    $response->assertForbidden();
});

it('returns 401 for unauthenticated mark-as-read request', function (): void {
    $user = User::factory()->artist()->create();
    $notification = Notification::factory()->for($user)->unread()->create();

    $response = $this->patchJson("/api/v1/notifications/{$notification->key}/read");

    $response->assertUnauthorized();
});

it('returns 404 for non-existent notification key', function (): void {
    $user = User::factory()->artist()->create();

    $response = $this->actingAs($user)->patchJson('/api/v1/notifications/ntf_nonexistent/read');

    $response->assertNotFound();
});

it('can mark an already-read notification as read again without error', function (): void {
    $user = User::factory()->artist()->create();
    $notification = Notification::factory()->for($user)->read()->create();

    $response = $this->actingAs($user)->patchJson("/api/v1/notifications/{$notification->key}/read");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.read_at', fn (string $value) => $value !== null);
});

// --- JSON:API structure ---

it('returns correct JSON:API links in notification resource', function (): void {
    $user = User::factory()->artist()->create();
    $notification = Notification::factory()->for($user)->create();

    $response = $this->actingAs($user)->getJson('/api/v1/notifications');

    $response->assertOk();
    $response->assertJsonPath('data.0.links.self.href', "/api/v1/notifications/{$notification->key}");
});

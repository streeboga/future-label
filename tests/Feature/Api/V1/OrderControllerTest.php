<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\OrderController;
use App\Models\Order;
use App\Models\Release;
use App\Models\ServiceCatalog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

covers(OrderController::class);

// --- POST /api/v1/orders (Create order) ---

it('allows artist to create an order for a service', function (): void {
    $artist = User::factory()->artist()->create();
    $service = ServiceCatalog::factory()->create();

    $response = $this->actingAs($artist)->postJson('/api/v1/orders', [
        'service_key' => $service->key,
        'notes' => 'Хочу мастеринг трека',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.type', 'orders');
    $response->assertJsonPath('data.attributes.status', 'pending');
    $response->assertJsonPath('data.attributes.notes', 'Хочу мастеринг трека');
    $response->assertHeader('Location');
});

it('returns created order with JSON:API structure', function (): void {
    $artist = User::factory()->artist()->create();
    $service = ServiceCatalog::factory()->create();

    $response = $this->actingAs($artist)->postJson('/api/v1/orders', [
        'service_key' => $service->key,
    ]);

    $response->assertCreated();
    $response->assertJsonStructure([
        'data' => [
            'type',
            'id',
            'attributes' => [
                'status',
                'notes',
                'created_at',
                'updated_at',
            ],
            'links' => ['self'],
        ],
    ]);
});

it('creates order with pending status by default', function (): void {
    $artist = User::factory()->artist()->create();
    $service = ServiceCatalog::factory()->create();

    $response = $this->actingAs($artist)->postJson('/api/v1/orders', [
        'service_key' => $service->key,
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.status', 'pending');
});

it('allows order with optional release_key', function (): void {
    $artist = User::factory()->artist()->create();
    $service = ServiceCatalog::factory()->create();
    $release = Release::factory()->create(['user_id' => $artist->id]);

    $response = $this->actingAs($artist)->postJson('/api/v1/orders', [
        'service_key' => $service->key,
        'release_key' => $release->key,
    ]);

    $response->assertCreated();
});

it('returns 422 for non-existent service_key', function (): void {
    $artist = User::factory()->artist()->create();

    $response = $this->actingAs($artist)->postJson('/api/v1/orders', [
        'service_key' => 'svc_nonexistent',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('service_key');
});

it('returns 422 when service_key is missing', function (): void {
    $artist = User::factory()->artist()->create();

    $response = $this->actingAs($artist)->postJson('/api/v1/orders', []);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('service_key');
});

it('returns 422 for non-existent release_key', function (): void {
    $artist = User::factory()->artist()->create();
    $service = ServiceCatalog::factory()->create();

    $response = $this->actingAs($artist)->postJson('/api/v1/orders', [
        'service_key' => $service->key,
        'release_key' => 'rel_nonexistent',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('release_key');
});

it('returns 401 for unauthenticated order creation', function (): void {
    $service = ServiceCatalog::factory()->create();

    $response = $this->postJson('/api/v1/orders', [
        'service_key' => $service->key,
    ]);

    $response->assertUnauthorized();
});

it('generates order key with ord_ prefix', function (): void {
    $artist = User::factory()->artist()->create();
    $service = ServiceCatalog::factory()->create();

    $response = $this->actingAs($artist)->postJson('/api/v1/orders', [
        'service_key' => $service->key,
    ]);

    $response->assertCreated();
    expect($response->json('data.id'))->toStartWith('ord_');
});

// --- GET /api/v1/orders (List own orders) ---

it('returns orders for authenticated user only', function (): void {
    $artist1 = User::factory()->artist()->create();
    $artist2 = User::factory()->artist()->create();
    $service = ServiceCatalog::factory()->create();

    Order::factory()->forUser($artist1)->forService($service)->create();
    Order::factory()->forUser($artist1)->forService($service)->create();
    Order::factory()->forUser($artist2)->forService($service)->create();

    $response = $this->actingAs($artist1)->getJson('/api/v1/orders');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('returns empty collection when user has no orders', function (): void {
    $artist = User::factory()->artist()->create();

    $response = $this->actingAs($artist)->getJson('/api/v1/orders');

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});

it('returns 401 for unauthenticated order list request', function (): void {
    $response = $this->getJson('/api/v1/orders');

    $response->assertUnauthorized();
});

it('returns orders sorted by created_at desc', function (): void {
    $artist = User::factory()->artist()->create();
    $service = ServiceCatalog::factory()->create();

    $older = Order::factory()->forUser($artist)->forService($service)->create([
        'created_at' => now()->subDay(),
    ]);
    $newer = Order::factory()->forUser($artist)->forService($service)->create([
        'created_at' => now(),
    ]);

    $response = $this->actingAs($artist)->getJson('/api/v1/orders');

    $response->assertOk();
    $response->assertJsonPath('data.0.id', $newer->key);
    $response->assertJsonPath('data.1.id', $older->key);
});

it('allows admin to list own orders', function (): void {
    $admin = User::factory()->admin()->create();
    $service = ServiceCatalog::factory()->create();
    Order::factory()->forUser($admin)->forService($service)->create();

    $response = $this->actingAs($admin)->getJson('/api/v1/orders');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
});

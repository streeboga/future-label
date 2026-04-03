<?php

declare(strict_types=1);

use App\Enums\ServiceCategory;
use App\Http\Controllers\Api\V1\ServiceCatalogController;
use App\Models\ServiceCatalog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

covers(ServiceCatalogController::class);

// --- GET /api/v1/services (Public list) ---

it('returns active services sorted by sort_order with JSON:API structure', function (): void {
    ServiceCatalog::factory()->withSortOrder(2)->create(['title' => 'Second']);
    ServiceCatalog::factory()->withSortOrder(1)->create(['title' => 'First']);
    ServiceCatalog::factory()->inactive()->create(['title' => 'Hidden']);

    $response = $this->getJson('/api/v1/services');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
    $response->assertJsonPath('data.0.attributes.title', 'First');
    $response->assertJsonPath('data.1.attributes.title', 'Second');
});

it('returns empty collection when no active services exist', function (): void {
    ServiceCatalog::factory()->inactive()->create();

    $response = $this->getJson('/api/v1/services');

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});

it('returns service with correct JSON:API structure', function (): void {
    ServiceCatalog::factory()->create([
        'title' => 'Мастеринг трека',
        'category' => ServiceCategory::Mastering,
        'price' => '5000.00',
        'currency' => 'RUB',
    ]);

    $response = $this->getJson('/api/v1/services');

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            [
                'type',
                'id',
                'attributes' => [
                    'title',
                    'description',
                    'price',
                    'currency',
                    'category',
                    'sort_order',
                    'is_active',
                    'created_at',
                    'updated_at',
                ],
                'links' => ['self'],
            ],
        ],
    ]);
    $response->assertJsonPath('data.0.type', 'services');
    $response->assertJsonPath('data.0.attributes.title', 'Мастеринг трека');
    $response->assertJsonPath('data.0.attributes.category', 'mastering');
});

it('does not require authentication for public service list', function (): void {
    ServiceCatalog::factory()->create();

    $response = $this->getJson('/api/v1/services');

    $response->assertOk();
});

// --- GET /api/v1/services/{service} (Show) ---

it('returns a single service by key', function (): void {
    $service = ServiceCatalog::factory()->create(['title' => 'Сведение']);

    $response = $this->getJson("/api/v1/services/{$service->key}");

    $response->assertOk();
    $response->assertJsonPath('data.type', 'services');
    $response->assertJsonPath('data.id', $service->key);
    $response->assertJsonPath('data.attributes.title', 'Сведение');
});

it('returns 404 for non-existent service key', function (): void {
    $response = $this->getJson('/api/v1/services/svc_nonexistent');

    $response->assertNotFound();
});

it('uses key as route model binding', function (): void {
    $service = ServiceCatalog::factory()->create();

    $response = $this->getJson("/api/v1/services/{$service->key}");

    $response->assertOk();
    expect($response->json('data.id'))->toStartWith('svc_');
});

// --- POST /api/v1/admin/services (Create) ---

it('allows admin to create a service', function (): void {
    $admin = User::factory()->admin()->create();

    $payload = [
        'title' => 'Новая услуга',
        'description' => 'Описание услуги',
        'price' => 3000.50,
        'currency' => 'RUB',
        'category' => 'production',
        'sort_order' => 5,
    ];

    $response = $this->actingAs($admin)->postJson('/api/v1/admin/services', $payload);

    $response->assertCreated();
    $response->assertJsonPath('data.type', 'services');
    $response->assertJsonPath('data.attributes.title', 'Новая услуга');
    $response->assertJsonPath('data.attributes.price', '3000.50');
    $response->assertJsonPath('data.attributes.category', 'production');
    $response->assertHeader('Location');
});

it('allows manager to create a service', function (): void {
    $manager = User::factory()->manager()->create();

    $payload = [
        'title' => 'Manager Service',
        'price' => 1000,
        'category' => 'mixing',
    ];

    $response = $this->actingAs($manager)->postJson('/api/v1/admin/services', $payload);

    $response->assertCreated();
});

it('forbids artist from creating a service', function (): void {
    $artist = User::factory()->artist()->create();

    $payload = [
        'title' => 'Hacked Service',
        'price' => 100,
        'category' => 'mastering',
    ];

    $response = $this->actingAs($artist)->postJson('/api/v1/admin/services', $payload);

    $response->assertForbidden();
});

it('returns 401 for unauthenticated service creation', function (): void {
    $payload = [
        'title' => 'Test',
        'price' => 100,
        'category' => 'mastering',
    ];

    $response = $this->postJson('/api/v1/admin/services', $payload);

    $response->assertUnauthorized();
});

it('returns 422 when required fields are missing on create', function (): void {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->postJson('/api/v1/admin/services', []);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('title');
    $response->assertJsonValidationErrorFor('price');
    $response->assertJsonValidationErrorFor('category');
});

it('returns 422 for invalid category on create', function (): void {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->postJson('/api/v1/admin/services', [
        'title' => 'Test',
        'price' => 100,
        'category' => 'invalid_category',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('category');
});

it('returns 422 for negative price', function (): void {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->postJson('/api/v1/admin/services', [
        'title' => 'Test',
        'price' => -100,
        'category' => 'mastering',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('price');
});

it('defaults is_active to true on create', function (): void {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->postJson('/api/v1/admin/services', [
        'title' => 'Active by default',
        'price' => 500,
        'category' => 'mixing',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.is_active', true);
});

it('defaults currency to RUB on create', function (): void {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->postJson('/api/v1/admin/services', [
        'title' => 'RUB default',
        'price' => 500,
        'category' => 'mixing',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.currency', 'RUB');
});

// --- PATCH /api/v1/admin/services/{service} (Update) ---

it('allows admin to update a service', function (): void {
    $admin = User::factory()->admin()->create();
    $service = ServiceCatalog::factory()->create(['title' => 'Old Title']);

    $response = $this->actingAs($admin)->patchJson("/api/v1/admin/services/{$service->key}", [
        'title' => 'New Title',
        'price' => 9999.99,
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.title', 'New Title');
    $response->assertJsonPath('data.attributes.price', '9999.99');
});

it('allows partial update of service', function (): void {
    $admin = User::factory()->admin()->create();
    $service = ServiceCatalog::factory()->create([
        'title' => 'Keep This',
        'price' => '1000.00',
    ]);

    $response = $this->actingAs($admin)->patchJson("/api/v1/admin/services/{$service->key}", [
        'price' => 2000,
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.title', 'Keep This');
    $response->assertJsonPath('data.attributes.price', '2000.00');
});

it('forbids artist from updating a service', function (): void {
    $artist = User::factory()->artist()->create();
    $service = ServiceCatalog::factory()->create();

    $response = $this->actingAs($artist)->patchJson("/api/v1/admin/services/{$service->key}", [
        'title' => 'Hacked',
    ]);

    $response->assertForbidden();
});

it('returns 404 when updating non-existent service', function (): void {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->patchJson('/api/v1/admin/services/svc_nonexistent', [
        'title' => 'Ghost',
    ]);

    $response->assertNotFound();
});

// --- DELETE /api/v1/admin/services/{service} (Soft deactivate) ---

it('allows admin to deactivate a service (soft delete)', function (): void {
    $admin = User::factory()->admin()->create();
    $service = ServiceCatalog::factory()->create(['is_active' => true]);

    $response = $this->actingAs($admin)->deleteJson("/api/v1/admin/services/{$service->key}");

    $response->assertNoContent();

    $service->refresh();
    expect($service->is_active)->toBeFalse();
});

it('deactivated service no longer appears in public list', function (): void {
    $admin = User::factory()->admin()->create();
    $service = ServiceCatalog::factory()->create(['is_active' => true]);

    $this->actingAs($admin)->deleteJson("/api/v1/admin/services/{$service->key}");

    $response = $this->getJson('/api/v1/services');
    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});

it('forbids artist from deactivating a service', function (): void {
    $artist = User::factory()->artist()->create();
    $service = ServiceCatalog::factory()->create();

    $response = $this->actingAs($artist)->deleteJson("/api/v1/admin/services/{$service->key}");

    $response->assertForbidden();
});

it('generates key with svc_ prefix', function (): void {
    $service = ServiceCatalog::factory()->create();

    expect($service->key)->toStartWith('svc_');
    expect(strlen($service->key))->toBeLessThanOrEqual(40);
});

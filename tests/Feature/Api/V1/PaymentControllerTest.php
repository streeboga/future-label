<?php

declare(strict_types=1);

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ReleaseStatus;
use App\Http\Controllers\Api\V1\AdminPaymentController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PaymentWebhookController;
use App\Models\Payment;
use App\Models\Release;
use App\Models\ServiceCatalog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

covers(PaymentController::class, PaymentWebhookController::class, AdminPaymentController::class);

// --- POST /api/v1/releases/{release}/pay (Initiate Payment) ---

it('creates an online payment for a release in awaiting_payment status', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->awaitingPayment()->create(['user_id' => $user->id]);
    $service = ServiceCatalog::factory()->create(['price' => 2500.00]);
    $release->services()->attach($service->id);

    $payload = [
        'method' => 'online',
        'return_url' => 'https://example.com/return',
    ];

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/pay", $payload);

    $response->assertStatus(201);
    $response->assertHeader('Location');
    $response->assertJsonStructure([
        'data' => [
            'type',
            'id',
            'attributes' => [
                'amount',
                'currency',
                'method',
                'status',
                'provider',
                'created_at',
                'updated_at',
            ],
            'links' => ['self'],
        ],
    ]);
    $response->assertJsonPath('data.type', 'payments');
    $response->assertJsonPath('data.attributes.method', 'online');
    $response->assertJsonPath('data.attributes.status', 'processing');
    $response->assertJsonPath('data.attributes.currency', 'RUB');
    $this->assertStringStartsWith('pay_', $response->json('data.id'));
    $this->assertDatabaseHas('payments', [
        'user_id' => $user->id,
        'release_id' => $release->id,
        'method' => 'online',
        'status' => 'processing',
    ]);
});

it('creates a manual payment for a release in awaiting_payment status', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->awaitingPayment()->create(['user_id' => $user->id]);

    $payload = [
        'method' => 'manual',
    ];

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/pay", $payload);

    $response->assertStatus(201);
    $response->assertJsonPath('data.attributes.method', 'manual');
    $response->assertJsonPath('data.attributes.status', 'pending');
    $this->assertDatabaseHas('payments', [
        'user_id' => $user->id,
        'release_id' => $release->id,
        'method' => 'manual',
        'status' => 'pending',
    ]);
});

it('rejects payment for release not in awaiting_payment status', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/pay", [
        'method' => 'online',
    ]);

    $response->assertUnprocessable();
});

it('returns 422 when method is missing', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->awaitingPayment()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/pay", []);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('method');
});

it('returns 422 when method is invalid', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->awaitingPayment()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/pay", [
        'method' => 'bitcoin',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('method');
});

it('returns 401 for unauthenticated payment creation', function (): void {
    $release = Release::factory()->awaitingPayment()->create();

    $response = $this->postJson("/api/v1/releases/{$release->key}/pay", [
        'method' => 'online',
    ]);

    $response->assertUnauthorized();
});

it('returns 404 for non-existent release payment', function (): void {
    $user = User::factory()->artist()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/releases/rel_nonexistent/pay', [
        'method' => 'online',
    ]);

    $response->assertNotFound();
});

it('generates key with pay_ prefix on payment create', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->awaitingPayment()->create(['user_id' => $user->id]);

    $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/pay", [
        'method' => 'manual',
    ]);

    $payment = Payment::first();
    expect($payment->key)->toStartWith('pay_');
});

// --- GET /api/v1/payments (Index) ---

it('returns paginated list of own payments for artist', function (): void {
    $user = User::factory()->artist()->create();
    Payment::factory()->count(3)->create(['user_id' => $user->id]);
    Payment::factory()->count(2)->create(); // other user's payments

    $response = $this->actingAs($user)->getJson('/api/v1/payments');

    $response->assertOk();
    $response->assertJsonCount(3, 'data');
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'type',
                'id',
                'attributes' => ['amount', 'currency', 'method', 'status', 'created_at', 'updated_at'],
                'links' => ['self'],
            ],
        ],
    ]);
    $response->assertJsonPath('data.0.type', 'payments');
});

it('returns empty data when user has no payments', function (): void {
    $user = User::factory()->artist()->create();
    Payment::factory()->count(2)->create(); // other user's payments

    $response = $this->actingAs($user)->getJson('/api/v1/payments');

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});

it('returns 401 for unauthenticated payment list', function (): void {
    $response = $this->getJson('/api/v1/payments');

    $response->assertUnauthorized();
});

it('paginates payments', function (): void {
    $user = User::factory()->artist()->create();
    Payment::factory()->count(20)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson('/api/v1/payments?per_page=5');

    $response->assertOk();
    $response->assertJsonCount(5, 'data');
});

// --- GET /api/v1/payments/{payment} (Show) ---

it('shows a payment with JSON:API structure', function (): void {
    $user = User::factory()->artist()->create();
    $payment = Payment::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson("/api/v1/payments/{$payment->key}");

    $response->assertOk();
    $response->assertJsonPath('data.type', 'payments');
    $response->assertJsonPath('data.id', $payment->key);
    $response->assertJsonStructure([
        'data' => [
            'type',
            'id',
            'attributes' => ['amount', 'currency', 'method', 'status'],
            'links' => ['self'],
        ],
    ]);
});

it('forbids artist from viewing another user payment', function (): void {
    $artist1 = User::factory()->artist()->create();
    $artist2 = User::factory()->artist()->create();
    $payment = Payment::factory()->create(['user_id' => $artist2->id]);

    $response = $this->actingAs($artist1)->getJson("/api/v1/payments/{$payment->key}");

    $response->assertForbidden();
});

it('allows admin to view any payment', function (): void {
    $admin = User::factory()->admin()->create();
    $payment = Payment::factory()->create();

    $response = $this->actingAs($admin)->getJson("/api/v1/payments/{$payment->key}");

    $response->assertOk();
});

it('allows manager to view any payment', function (): void {
    $manager = User::factory()->manager()->create();
    $payment = Payment::factory()->create();

    $response = $this->actingAs($manager)->getJson("/api/v1/payments/{$payment->key}");

    $response->assertOk();
});

it('returns 404 for non-existent payment', function (): void {
    $user = User::factory()->artist()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/payments/pay_nonexistent');

    $response->assertNotFound();
});

// --- POST /api/v1/payments/webhook (Webhook) ---

it('processes webhook and updates payment status to paid', function (): void {
    $payment = Payment::factory()->processing()->online()->create([
        'provider_payment_id' => 'stub_test123',
    ]);

    $response = $this->postJson('/api/v1/payments/webhook', [
        'payment_id' => 'stub_test123',
        'status' => 'paid',
        'receipt_url' => 'https://provider.com/receipt/123',
    ], [
        'X-Webhook-Secret' => 'stub-secret',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'paid');
    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'status' => 'paid',
        'receipt_url' => 'https://provider.com/receipt/123',
    ]);
});

it('transitions release to in_review after successful payment', function (): void {
    $release = Release::factory()->awaitingPayment()->create();
    $payment = Payment::factory()->processing()->online()->create([
        'user_id' => $release->user_id,
        'release_id' => $release->id,
        'provider_payment_id' => 'stub_transition_test',
    ]);

    $this->postJson('/api/v1/payments/webhook', [
        'payment_id' => 'stub_transition_test',
        'status' => 'paid',
    ], [
        'X-Webhook-Secret' => 'stub-secret',
    ]);

    $release->refresh();
    expect($release->status)->toBe(ReleaseStatus::InReview);
});

it('rejects webhook with invalid signature', function (): void {
    $response = $this->postJson('/api/v1/payments/webhook', [
        'payment_id' => 'stub_test123',
        'status' => 'paid',
    ], [
        'X-Webhook-Secret' => 'wrong-secret',
    ]);

    $response->assertForbidden();
});

it('rejects webhook with missing payment_id', function (): void {
    $response = $this->postJson('/api/v1/payments/webhook', [
        'status' => 'paid',
    ], [
        'X-Webhook-Secret' => 'stub-secret',
    ]);

    $response->assertUnprocessable();
});

it('rejects webhook with unknown payment_id', function (): void {
    $response = $this->postJson('/api/v1/payments/webhook', [
        'payment_id' => 'nonexistent_id',
        'status' => 'paid',
    ], [
        'X-Webhook-Secret' => 'stub-secret',
    ]);

    $response->assertUnprocessable();
});

it('rejects webhook with invalid status transition', function (): void {
    Payment::factory()->paid()->create([
        'provider_payment_id' => 'stub_invalid_transition',
    ]);

    $response = $this->postJson('/api/v1/payments/webhook', [
        'payment_id' => 'stub_invalid_transition',
        'status' => 'processing',
    ], [
        'X-Webhook-Secret' => 'stub-secret',
    ]);

    $response->assertUnprocessable();
});

it('webhook does not require authentication', function (): void {
    $payment = Payment::factory()->processing()->online()->create([
        'provider_payment_id' => 'stub_no_auth',
    ]);

    $response = $this->postJson('/api/v1/payments/webhook', [
        'payment_id' => 'stub_no_auth',
        'status' => 'paid',
    ], [
        'X-Webhook-Secret' => 'stub-secret',
    ]);

    // Should not return 401 since the route is public
    $response->assertOk();
});

// --- PATCH /api/v1/admin/payments/{payment}/confirm (Admin confirm) ---

it('allows manager to confirm a manual pending payment', function (): void {
    $manager = User::factory()->manager()->create();
    $payment = Payment::factory()->manual()->pending()->create();

    $response = $this->actingAs($manager)->patchJson("/api/v1/admin/payments/{$payment->key}/confirm");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'confirmed');
    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'status' => 'confirmed',
        'confirmed_by' => $manager->id,
    ]);
});

it('allows admin to confirm a manual pending payment', function (): void {
    $admin = User::factory()->admin()->create();
    $payment = Payment::factory()->manual()->pending()->create();

    $response = $this->actingAs($admin)->patchJson("/api/v1/admin/payments/{$payment->key}/confirm");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'confirmed');
});

it('transitions release to in_review after manual payment confirmation', function (): void {
    $manager = User::factory()->manager()->create();
    $release = Release::factory()->awaitingPayment()->create();
    $payment = Payment::factory()->manual()->pending()->create([
        'user_id' => $release->user_id,
        'release_id' => $release->id,
    ]);

    $this->actingAs($manager)->patchJson("/api/v1/admin/payments/{$payment->key}/confirm");

    $release->refresh();
    expect($release->status)->toBe(ReleaseStatus::InReview);
});

it('rejects confirming an online payment', function (): void {
    $manager = User::factory()->manager()->create();
    $payment = Payment::factory()->online()->pending()->create();

    $response = $this->actingAs($manager)->patchJson("/api/v1/admin/payments/{$payment->key}/confirm");

    $response->assertUnprocessable();
});

it('rejects confirming an already confirmed payment', function (): void {
    $manager = User::factory()->manager()->create();
    $payment = Payment::factory()->manual()->confirmed()->create();

    $response = $this->actingAs($manager)->patchJson("/api/v1/admin/payments/{$payment->key}/confirm");

    $response->assertUnprocessable();
});

it('forbids artist from confirming payment', function (): void {
    $artist = User::factory()->artist()->create();
    $payment = Payment::factory()->manual()->pending()->create();

    $response = $this->actingAs($artist)->patchJson("/api/v1/admin/payments/{$payment->key}/confirm");

    $response->assertForbidden();
});

it('returns 401 for unauthenticated payment confirmation', function (): void {
    $payment = Payment::factory()->manual()->pending()->create();

    $response = $this->patchJson("/api/v1/admin/payments/{$payment->key}/confirm");

    $response->assertUnauthorized();
});

// --- PATCH /api/v1/admin/payments/{payment}/reject (Admin reject) ---

it('allows manager to reject a pending payment', function (): void {
    $manager = User::factory()->manager()->create();
    $payment = Payment::factory()->manual()->pending()->create();

    $response = $this->actingAs($manager)->patchJson("/api/v1/admin/payments/{$payment->key}/reject");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'failed');
    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'status' => 'failed',
    ]);
});

it('allows admin to reject a pending payment', function (): void {
    $admin = User::factory()->admin()->create();
    $payment = Payment::factory()->pending()->create();

    $response = $this->actingAs($admin)->patchJson("/api/v1/admin/payments/{$payment->key}/reject");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'failed');
});

it('rejects rejecting a terminal payment', function (): void {
    $manager = User::factory()->manager()->create();
    $payment = Payment::factory()->refunded()->create();

    $response = $this->actingAs($manager)->patchJson("/api/v1/admin/payments/{$payment->key}/reject");

    $response->assertUnprocessable();
});

it('forbids artist from rejecting payment', function (): void {
    $artist = User::factory()->artist()->create();
    $payment = Payment::factory()->pending()->create();

    $response = $this->actingAs($artist)->patchJson("/api/v1/admin/payments/{$payment->key}/reject");

    $response->assertForbidden();
});

// --- PaymentStatus enum transitions ---

it('validates payment status transitions correctly', function (): void {
    expect(PaymentStatus::Pending->canTransitionTo(PaymentStatus::Processing))->toBeTrue();
    expect(PaymentStatus::Pending->canTransitionTo(PaymentStatus::Paid))->toBeTrue();
    expect(PaymentStatus::Pending->canTransitionTo(PaymentStatus::Failed))->toBeTrue();
    expect(PaymentStatus::Processing->canTransitionTo(PaymentStatus::Paid))->toBeTrue();
    expect(PaymentStatus::Processing->canTransitionTo(PaymentStatus::Failed))->toBeTrue();
    expect(PaymentStatus::Paid->canTransitionTo(PaymentStatus::Confirmed))->toBeTrue();
    expect(PaymentStatus::Paid->canTransitionTo(PaymentStatus::Refunded))->toBeTrue();
    expect(PaymentStatus::Confirmed->canTransitionTo(PaymentStatus::Refunded))->toBeTrue();
    expect(PaymentStatus::Refunded->canTransitionTo(PaymentStatus::Pending))->toBeFalse();
    expect(PaymentStatus::Failed->canTransitionTo(PaymentStatus::Pending))->toBeTrue();
});

// --- PaymentMethod enum ---

it('validates payment method enum values', function (): void {
    expect(PaymentMethod::values())->toBe(['online', 'manual']);
    expect(PaymentMethod::Online->getLabel())->toBe('Онлайн оплата');
    expect(PaymentMethod::Manual->getLabel())->toBe('Ручная оплата');
});

// --- PaymentStatus enum labels ---

it('validates payment status enum labels and colors', function (): void {
    expect(PaymentStatus::Pending->getLabel())->toBe('Ожидание');
    expect(PaymentStatus::Paid->getColor())->toBe('success');
    expect(PaymentStatus::Failed->getIcon())->toBe('heroicon-o-x-circle');
});

// --- Amount calculation ---

it('calculates amount from release services', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->awaitingPayment()->create(['user_id' => $user->id]);

    $service1 = ServiceCatalog::factory()->create(['price' => 1500.00]);
    $service2 = ServiceCatalog::factory()->create(['price' => 2500.00]);
    $release->services()->attach([$service1->id, $service2->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/pay", [
        'method' => 'manual',
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.attributes.amount', '4000.00');
});

it('uses default amount when release has no services', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->awaitingPayment()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/pay", [
        'method' => 'manual',
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.attributes.amount', '1000.00');
});

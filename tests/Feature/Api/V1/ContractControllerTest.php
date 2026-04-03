<?php

declare(strict_types=1);

use App\Enums\ContractStatus;
use App\Http\Controllers\Api\V1\ContractController;
use App\Models\Contract;
use App\Models\Release;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

covers(ContractController::class);

// --- POST /api/v1/releases/{release}/contract (Generate) ---

it('generates a contract for a release and returns 201 with location header', function (): void {
    Storage::fake('local');

    $user = User::factory()->artist()->create();
    $release = Release::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/contract");

    $response->assertStatus(201);
    $response->assertHeader('Location');
    $response->assertJsonStructure([
        'data' => [
            'type',
            'id',
            'attributes' => [
                'template_version',
                'pdf_url',
                'status',
                'created_at',
                'updated_at',
            ],
            'links' => ['self'],
        ],
    ]);
    $response->assertJsonPath('data.type', 'contracts');
    $response->assertJsonPath('data.attributes.status', 'pending');
    $response->assertJsonPath('data.attributes.template_version', '1.0');

    $this->assertDatabaseHas('contracts', [
        'user_id' => $user->id,
        'release_id' => $release->id,
        'status' => 'pending',
    ]);
});

it('generates contract key with ctr_ prefix', function (): void {
    Storage::fake('local');

    $user = User::factory()->artist()->create();
    $release = Release::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/contract");

    $contract = Contract::first();
    expect($contract->key)->toStartWith('ctr_');
});

it('generates contract with custom template_version', function (): void {
    Storage::fake('local');

    $user = User::factory()->artist()->create();
    $release = Release::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/contract", [
        'template_version' => '2.0',
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.attributes.template_version', '2.0');
});

it('generates PDF file on contract creation', function (): void {
    Storage::fake('local');

    $user = User::factory()->artist()->create();
    $release = Release::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/contract");

    $response->assertStatus(201);

    $pdfUrl = $response->json('data.attributes.pdf_url');
    expect($pdfUrl)->not->toBeNull();

    Storage::disk('local')->assertExists($pdfUrl);
});

it('returns 401 for unauthenticated contract generation', function (): void {
    $release = Release::factory()->create();

    $response = $this->postJson("/api/v1/releases/{$release->key}/contract");

    $response->assertUnauthorized();
});

it('returns 422 when generating contract for another user release', function (): void {
    Storage::fake('local');

    $artist1 = User::factory()->artist()->create();
    $artist2 = User::factory()->artist()->create();
    $release = Release::factory()->create(['user_id' => $artist2->id]);

    $response = $this->actingAs($artist1)->postJson("/api/v1/releases/{$release->key}/contract");

    $response->assertUnprocessable();
});

it('returns 422 when release already has accepted contract', function (): void {
    Storage::fake('local');

    $user = User::factory()->artist()->create();
    $release = Release::factory()->create(['user_id' => $user->id]);
    Contract::factory()->accepted()->create([
        'user_id' => $user->id,
        'release_id' => $release->id,
    ]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/contract");

    $response->assertUnprocessable();
});

it('returns 404 when release does not exist', function (): void {
    $user = User::factory()->artist()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/releases/rel_nonexistent/contract');

    $response->assertNotFound();
});

// --- PATCH /api/v1/contracts/{contract}/accept ---

it('accepts a pending contract and records IP and user-agent', function (): void {
    $user = User::factory()->artist()->create();
    $contract = Contract::factory()->pending()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->withHeaders([
            'User-Agent' => 'TestBrowser/2.0',
        ])
        ->patchJson("/api/v1/contracts/{$contract->key}/accept");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'accepted');
    $response->assertJsonPath('data.attributes.accepted_at', fn ($v) => $v !== null);
    $response->assertJsonPath('data.attributes.accepted_ip', '127.0.0.1');

    $this->assertDatabaseHas('contracts', [
        'id' => $contract->id,
        'status' => 'accepted',
        'accepted_user_agent' => 'TestBrowser/2.0',
    ]);
});

it('returns 422 when accepting non-pending contract', function (): void {
    $user = User::factory()->artist()->create();
    $contract = Contract::factory()->accepted()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->patchJson("/api/v1/contracts/{$contract->key}/accept");

    $response->assertUnprocessable();
});

it('returns 422 when accepting expired contract', function (): void {
    $user = User::factory()->artist()->create();
    $contract = Contract::factory()->expired()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->patchJson("/api/v1/contracts/{$contract->key}/accept");

    $response->assertUnprocessable();
});

it('returns 422 when accepting revoked contract', function (): void {
    $user = User::factory()->artist()->create();
    $contract = Contract::factory()->revoked()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->patchJson("/api/v1/contracts/{$contract->key}/accept");

    $response->assertUnprocessable();
});

it('forbids other user from accepting contract', function (): void {
    $artist1 = User::factory()->artist()->create();
    $artist2 = User::factory()->artist()->create();
    $contract = Contract::factory()->pending()->create(['user_id' => $artist2->id]);

    $response = $this->actingAs($artist1)->patchJson("/api/v1/contracts/{$contract->key}/accept");

    $response->assertForbidden();
});

it('returns 401 for unauthenticated accept', function (): void {
    $contract = Contract::factory()->pending()->create();

    $response = $this->patchJson("/api/v1/contracts/{$contract->key}/accept");

    $response->assertUnauthorized();
});

it('returns 404 for non-existent contract accept', function (): void {
    $user = User::factory()->artist()->create();

    $response = $this->actingAs($user)->patchJson('/api/v1/contracts/ctr_nonexistent/accept');

    $response->assertNotFound();
});

// --- GET /api/v1/contracts (Index) ---

it('returns paginated list of own contracts for artist', function (): void {
    $user = User::factory()->artist()->create();
    Contract::factory()->count(3)->create(['user_id' => $user->id]);
    Contract::factory()->count(2)->create(); // other user's contracts

    $response = $this->actingAs($user)->getJson('/api/v1/contracts');

    $response->assertOk();
    $response->assertJsonCount(3, 'data');
});

it('returns all contracts for admin', function (): void {
    $admin = User::factory()->admin()->create();
    Contract::factory()->count(5)->create();

    $response = $this->actingAs($admin)->getJson('/api/v1/contracts');

    $response->assertOk();
    $response->assertJsonCount(5, 'data');
});

it('returns all contracts for manager', function (): void {
    $manager = User::factory()->manager()->create();
    Contract::factory()->count(4)->create();

    $response = $this->actingAs($manager)->getJson('/api/v1/contracts');

    $response->assertOk();
    $response->assertJsonCount(4, 'data');
});

it('filters contracts by status', function (): void {
    $user = User::factory()->artist()->create();
    Contract::factory()->count(2)->pending()->create(['user_id' => $user->id]);
    Contract::factory()->accepted()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson('/api/v1/contracts?filter[status]=pending');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('returns 401 for unauthenticated contract list', function (): void {
    $response = $this->getJson('/api/v1/contracts');

    $response->assertUnauthorized();
});

it('paginates contracts', function (): void {
    $user = User::factory()->artist()->create();
    Contract::factory()->count(20)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson('/api/v1/contracts?per_page=5');

    $response->assertOk();
    $response->assertJsonCount(5, 'data');
});

it('returns contract with JSON:API structure', function (): void {
    $user = User::factory()->artist()->create();
    $contract = Contract::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson('/api/v1/contracts');

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'type',
                'id',
                'attributes' => [
                    'template_version',
                    'status',
                    'created_at',
                ],
                'links' => ['self'],
            ],
        ],
    ]);
    $response->assertJsonPath('data.0.type', 'contracts');
});

// --- GET /api/v1/contracts/{contract}/pdf (Download PDF) ---

it('downloads contract PDF', function (): void {
    Storage::fake('local');

    $user = User::factory()->artist()->create();
    $pdfPath = 'contracts/ctr_test.pdf';
    Storage::disk('local')->put($pdfPath, 'fake-pdf-content');

    $contract = Contract::factory()->withPdf()->create([
        'user_id' => $user->id,
        'pdf_url' => $pdfPath,
    ]);

    $response = $this->actingAs($user)->get("/api/v1/contracts/{$contract->key}/pdf");

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
});

it('returns 422 when contract has no PDF', function (): void {
    $user = User::factory()->artist()->create();
    $contract = Contract::factory()->create([
        'user_id' => $user->id,
        'pdf_url' => null,
    ]);

    $response = $this->actingAs($user)->getJson("/api/v1/contracts/{$contract->key}/pdf");

    $response->assertUnprocessable();
});

it('forbids other user from downloading contract PDF', function (): void {
    $artist1 = User::factory()->artist()->create();
    $artist2 = User::factory()->artist()->create();
    $contract = Contract::factory()->withPdf()->create(['user_id' => $artist2->id]);

    $response = $this->actingAs($artist1)->get("/api/v1/contracts/{$contract->key}/pdf");

    $response->assertForbidden();
});

it('allows admin to download any contract PDF', function (): void {
    Storage::fake('local');

    $admin = User::factory()->admin()->create();
    $pdfPath = 'contracts/ctr_admin_test.pdf';
    Storage::disk('local')->put($pdfPath, 'fake-pdf-content');

    $contract = Contract::factory()->withPdf()->create(['pdf_url' => $pdfPath]);

    $response = $this->actingAs($admin)->get("/api/v1/contracts/{$contract->key}/pdf");

    $response->assertOk();
});

it('returns 401 for unauthenticated PDF download', function (): void {
    $contract = Contract::factory()->withPdf()->create();

    $response = $this->getJson("/api/v1/contracts/{$contract->key}/pdf");

    $response->assertUnauthorized();
});

// --- Release submit requires accepted contract ---

it('rejects release submit without accepted contract', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/submit");

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('contract');
});

it('allows release submit with accepted contract', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);
    Contract::factory()->accepted()->create([
        'user_id' => $user->id,
        'release_id' => $release->id,
    ]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/submit");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'in_review');
});

it('rejects release submit with only pending contract', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);
    Contract::factory()->pending()->create([
        'user_id' => $user->id,
        'release_id' => $release->id,
    ]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/submit");

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('contract');
});

// --- ContractStatus enum ---

it('validates contract status enum values', function (): void {
    expect(ContractStatus::values())->toBe(['pending', 'accepted', 'expired', 'revoked']);
});

it('returns correct labels for contract statuses', function (): void {
    expect(ContractStatus::Pending->getLabel())->toBe('Ожидает подписания');
    expect(ContractStatus::Accepted->getLabel())->toBe('Подписан');
    expect(ContractStatus::Expired->getLabel())->toBe('Истёк');
    expect(ContractStatus::Revoked->getLabel())->toBe('Отозван');
});

it('returns correct colors for contract statuses', function (): void {
    expect(ContractStatus::Pending->getColor())->toBe('warning');
    expect(ContractStatus::Accepted->getColor())->toBe('success');
    expect(ContractStatus::Expired->getColor())->toBe('gray');
    expect(ContractStatus::Revoked->getColor())->toBe('danger');
});

it('returns correct icons for contract statuses', function (): void {
    expect(ContractStatus::Pending->getIcon())->toBe('heroicon-o-clock');
    expect(ContractStatus::Accepted->getIcon())->toBe('heroicon-o-check-circle');
    expect(ContractStatus::Expired->getIcon())->toBe('heroicon-o-calendar');
    expect(ContractStatus::Revoked->getIcon())->toBe('heroicon-o-x-circle');
});

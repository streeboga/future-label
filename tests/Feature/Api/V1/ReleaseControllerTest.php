<?php

declare(strict_types=1);

use App\Enums\ReleaseStatus;
use App\Http\Controllers\Api\V1\ReleaseController;
use App\Models\Contract;
use App\Models\Release;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

covers(ReleaseController::class);

// --- POST /api/v1/releases (Store) ---

it('creates a release in draft status and returns 201 with location header', function (): void {
    $user = User::factory()->artist()->create();

    $payload = [
        'title' => 'My First Single',
        'type' => 'single',
        'artist_name' => 'DJ Test',
        'genre' => 'electronic',
        'language' => 'en',
    ];

    $response = $this->actingAs($user)->postJson('/api/v1/releases', $payload);

    $response->assertStatus(201);
    $response->assertHeader('Location');
    $response->assertJsonStructure([
        'data' => [
            'type',
            'id',
            'attributes' => [
                'title',
                'artist_name',
                'type',
                'genre',
                'language',
                'status',
                'created_at',
                'updated_at',
            ],
            'links' => ['self'],
        ],
    ]);
    $response->assertJsonPath('data.type', 'releases');
    $response->assertJsonPath('data.attributes.title', 'My First Single');
    $response->assertJsonPath('data.attributes.status', 'draft');
    $response->assertJsonPath('data.attributes.type', 'single');

    $this->assertDatabaseHas('releases', [
        'user_id' => $user->id,
        'title' => 'My First Single',
        'status' => 'draft',
    ]);
});

it('returns 401 for unauthenticated release creation', function (): void {
    $response = $this->postJson('/api/v1/releases', [
        'title' => 'Unauthorized Release',
        'type' => 'single',
    ]);

    $response->assertUnauthorized();
});

it('returns 422 when title is missing on create', function (): void {
    $user = User::factory()->artist()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/releases', [
        'type' => 'single',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('title');
});

it('returns 422 when type is missing on create', function (): void {
    $user = User::factory()->artist()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/releases', [
        'title' => 'Missing Type',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('type');
});

it('returns 422 when type is invalid', function (): void {
    $user = User::factory()->artist()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/releases', [
        'title' => 'Bad Type',
        'type' => 'mixtape',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('type');
});

it('creates release with all optional fields', function (): void {
    $user = User::factory()->artist()->create();

    $payload = [
        'title' => 'Full Release',
        'type' => 'album',
        'artist_name' => 'Full Artist',
        'genre' => 'rock',
        'language' => 'ru',
        'description' => 'A great album',
        'release_date' => now()->addMonth()->toDateString(),
        'metadata' => ['upc' => '123456789012'],
    ];

    $response = $this->actingAs($user)->postJson('/api/v1/releases', $payload);

    $response->assertStatus(201);
    $response->assertJsonPath('data.attributes.type', 'album');
    $response->assertJsonPath('data.attributes.genre', 'rock');
    $response->assertJsonPath('data.attributes.description', 'A great album');
});

it('generates key with rel_ prefix on create', function (): void {
    $user = User::factory()->artist()->create();

    $this->actingAs($user)->postJson('/api/v1/releases', [
        'title' => 'Key Check',
        'type' => 'single',
    ]);

    $release = Release::first();
    expect($release->key)->toStartWith('rel_');
});

it('strips HTML tags from title on create', function (): void {
    $user = User::factory()->artist()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/releases', [
        'title' => '<script>alert("xss")</script>Clean Title',
        'type' => 'single',
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.attributes.title', 'alert("xss")Clean Title');
});

// --- GET /api/v1/releases (Index) ---

it('returns paginated list of own releases for artist', function (): void {
    $user = User::factory()->artist()->create();
    Release::factory()->count(3)->create(['user_id' => $user->id]);
    Release::factory()->count(2)->create(); // other user's releases

    $response = $this->actingAs($user)->getJson('/api/v1/releases');

    $response->assertOk();
    $response->assertJsonCount(3, 'data');
});

it('returns all releases for admin', function (): void {
    $admin = User::factory()->admin()->create();
    Release::factory()->count(5)->create();

    $response = $this->actingAs($admin)->getJson('/api/v1/releases');

    $response->assertOk();
    $response->assertJsonCount(5, 'data');
});

it('returns all releases for manager', function (): void {
    $manager = User::factory()->manager()->create();
    Release::factory()->count(4)->create();

    $response = $this->actingAs($manager)->getJson('/api/v1/releases');

    $response->assertOk();
    $response->assertJsonCount(4, 'data');
});

it('filters releases by status', function (): void {
    $user = User::factory()->artist()->create();
    Release::factory()->count(2)->draft()->create(['user_id' => $user->id]);
    Release::factory()->inReview()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson('/api/v1/releases?filter[status]=draft');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('returns 401 for unauthenticated release list', function (): void {
    $response = $this->getJson('/api/v1/releases');

    $response->assertUnauthorized();
});

it('paginates releases', function (): void {
    $user = User::factory()->artist()->create();
    Release::factory()->count(20)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson('/api/v1/releases?per_page=5');

    $response->assertOk();
    $response->assertJsonCount(5, 'data');
});

// --- GET /api/v1/releases/{release} (Show) ---

it('shows a release with JSON:API structure', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson("/api/v1/releases/{$release->key}");

    $response->assertOk();
    $response->assertJsonPath('data.type', 'releases');
    $response->assertJsonPath('data.id', $release->key);
    $response->assertJsonStructure([
        'data' => [
            'type',
            'id',
            'attributes' => [
                'title',
                'status',
                'type',
            ],
            'links' => ['self'],
        ],
    ]);
});

it('forbids artist from viewing another user release', function (): void {
    $artist1 = User::factory()->artist()->create();
    $artist2 = User::factory()->artist()->create();
    $release = Release::factory()->create(['user_id' => $artist2->id]);

    $response = $this->actingAs($artist1)->getJson("/api/v1/releases/{$release->key}");

    $response->assertForbidden();
});

it('allows admin to view any release', function (): void {
    $admin = User::factory()->admin()->create();
    $release = Release::factory()->create();

    $response = $this->actingAs($admin)->getJson("/api/v1/releases/{$release->key}");

    $response->assertOk();
});

it('returns 404 for non-existent release', function (): void {
    $user = User::factory()->artist()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/releases/rel_nonexistent');

    $response->assertNotFound();
});

// --- PATCH /api/v1/releases/{release} (Update) ---

it('updates release title', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->patchJson("/api/v1/releases/{$release->key}", [
        'title' => 'Updated Title',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.title', 'Updated Title');
});

it('updates release with partial fields (autosave)', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create([
        'user_id' => $user->id,
        'title' => 'Original',
        'genre' => 'pop',
    ]);

    $response = $this->actingAs($user)->patchJson("/api/v1/releases/{$release->key}", [
        'genre' => 'rock',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.genre', 'rock');
    $response->assertJsonPath('data.attributes.title', 'Original');
});

it('updates cover_url on release', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->patchJson("/api/v1/releases/{$release->key}", [
        'cover_url' => 'https://storage.example.com/covers/test.jpg',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.cover_url', 'https://storage.example.com/covers/test.jpg');
});

it('updates metadata on release', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->patchJson("/api/v1/releases/{$release->key}", [
        'metadata' => ['upc' => '123456789012', 'label' => 'Test Label'],
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.metadata.upc', '123456789012');
    $response->assertJsonPath('data.attributes.metadata.label', 'Test Label');
});

it('rejects update on non-draft release', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->inReview()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->patchJson("/api/v1/releases/{$release->key}", [
        'title' => 'Cannot Update',
    ]);

    $response->assertUnprocessable();
});

it('allows update on rejected release', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->rejected()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->patchJson("/api/v1/releases/{$release->key}", [
        'title' => 'Fixed Title',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.title', 'Fixed Title');
});

it('forbids artist from updating another user release', function (): void {
    $artist1 = User::factory()->artist()->create();
    $artist2 = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $artist2->id]);

    $response = $this->actingAs($artist1)->patchJson("/api/v1/releases/{$release->key}", [
        'title' => 'Hacked',
    ]);

    $response->assertForbidden();
});

it('accepts empty body for release update without changes', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create([
        'user_id' => $user->id,
        'title' => 'Existing Title',
    ]);

    $response = $this->actingAs($user)->patchJson("/api/v1/releases/{$release->key}", []);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.title', 'Existing Title');
});

// --- DELETE /api/v1/releases/{release} (Destroy) ---

it('deletes a draft release and returns 204', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson("/api/v1/releases/{$release->key}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('releases', ['id' => $release->id]);
});

it('rejects deletion of non-draft release', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->inReview()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson("/api/v1/releases/{$release->key}");

    $response->assertUnprocessable();
});

it('forbids artist from deleting another user release', function (): void {
    $artist1 = User::factory()->artist()->create();
    $artist2 = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $artist2->id]);

    $response = $this->actingAs($artist1)->deleteJson("/api/v1/releases/{$release->key}");

    $response->assertForbidden();
});

// --- POST /api/v1/releases/{release}/submit (Submit) ---

it('submits a draft release and transitions to in_review', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);
    Contract::factory()->accepted()->create([
        'user_id' => $user->id,
        'release_id' => $release->id,
    ]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/submit");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'in_review');
    $response->assertJsonPath('data.attributes.submitted_at', fn ($v) => $v !== null);
});

it('resubmits a rejected release (transition rejected → draft → in_review)', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->rejected()->create(['user_id' => $user->id]);
    Contract::factory()->accepted()->create([
        'user_id' => $user->id,
        'release_id' => $release->id,
    ]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/submit");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'in_review');
    $response->assertJsonPath('data.attributes.reject_reason', null);
});

it('rejects submit of published release', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->published()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/submit");

    $response->assertUnprocessable();
});

it('rejects submit of in_review release', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->inReview()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/submit");

    $response->assertUnprocessable();
});

it('forbids other user from submitting release', function (): void {
    $artist1 = User::factory()->artist()->create();
    $artist2 = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $artist2->id]);

    $response = $this->actingAs($artist1)->postJson("/api/v1/releases/{$release->key}/submit");

    $response->assertForbidden();
});

// --- ReleaseStatus enum transitions ---

it('validates status transitions correctly', function (): void {
    expect(ReleaseStatus::Draft->canTransitionTo(ReleaseStatus::InReview))->toBeTrue();
    expect(ReleaseStatus::Draft->canTransitionTo(ReleaseStatus::AwaitingPayment))->toBeTrue();
    expect(ReleaseStatus::Draft->canTransitionTo(ReleaseStatus::Published))->toBeFalse();
    expect(ReleaseStatus::InReview->canTransitionTo(ReleaseStatus::Approved))->toBeTrue();
    expect(ReleaseStatus::InReview->canTransitionTo(ReleaseStatus::Rejected))->toBeTrue();
    expect(ReleaseStatus::Rejected->canTransitionTo(ReleaseStatus::Draft))->toBeTrue();
    expect(ReleaseStatus::Published->canTransitionTo(ReleaseStatus::Draft))->toBeFalse();
});

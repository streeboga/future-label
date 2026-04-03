<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

covers(ProfileController::class);

// --- GET /api/v1/profile (Show) ---

it('returns authenticated user profile with JSON:API structure', function (): void {
    $user = User::factory()->artist()->create([
        'name' => 'Иван Иванов',
        'email' => 'ivan@example.com',
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/profile');

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'type',
            'id',
            'attributes' => [
                'name',
                'email',
                'role',
                'stage_name',
                'phone',
                'telegram',
                'passport_data',
                'bank_details',
                'created_at',
                'updated_at',
            ],
            'links' => ['self'],
        ],
    ]);
    $response->assertJsonPath('data.type', 'users');
    $response->assertJsonPath('data.attributes.name', 'Иван Иванов');
    $response->assertJsonPath('data.attributes.email', 'ivan@example.com');
    $response->assertJsonPath('data.attributes.role', 'artist');
});

it('returns 401 for unauthenticated profile request', function (): void {
    $response = $this->getJson('/api/v1/profile');

    $response->assertUnauthorized();
});

it('logs PII access when profile is read', function (): void {
    $user = User::factory()->artist()->create();

    $this->actingAs($user)->getJson('/api/v1/profile');

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $user->id,
        'subject_type' => User::class,
        'subject_id' => $user->id,
        'action' => 'profile.viewed',
    ]);
});

it('returns encrypted PII fields as decrypted values for own profile', function (): void {
    $user = User::factory()->artist()->create([
        'passport_data' => 'Паспорт: 1234 567890',
        'bank_details' => 'Р/С: 40817810099910004312',
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/profile');

    $response->assertOk();
    $response->assertJsonPath('data.attributes.passport_data', 'Паспорт: 1234 567890');
    $response->assertJsonPath('data.attributes.bank_details', 'Р/С: 40817810099910004312');
});

it('allows admin to view any user profile', function (): void {
    $admin = User::factory()->admin()->create();
    $artist = User::factory()->artist()->create();

    $response = $this->actingAs($admin)->getJson("/api/v1/profile/{$artist->key}");

    $response->assertOk();
    $response->assertJsonPath('data.id', $artist->key);
});

it('forbids artist from viewing other user profile', function (): void {
    $artist1 = User::factory()->artist()->create();
    $artist2 = User::factory()->artist()->create();

    $response = $this->actingAs($artist1)->getJson("/api/v1/profile/{$artist2->key}");

    $response->assertForbidden();
});

// --- PATCH /api/v1/profile (Update) ---

it('updates profile fields and returns updated resource', function (): void {
    $user = User::factory()->artist()->create();

    $payload = [
        'stage_name' => 'DJ Ivan',
        'phone' => '+79001234567',
        'telegram' => '@dj_ivan',
        'passport_data' => 'Паспорт: 1234 567890',
        'bank_details' => 'Р/С: 40817810099910004312',
    ];

    $response = $this->actingAs($user)->patchJson('/api/v1/profile', $payload);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.stage_name', 'DJ Ivan');
    $response->assertJsonPath('data.attributes.phone', '+79001234567');
    $response->assertJsonPath('data.attributes.telegram', '@dj_ivan');
    $response->assertJsonPath('data.attributes.passport_data', 'Паспорт: 1234 567890');
    $response->assertJsonPath('data.attributes.bank_details', 'Р/С: 40817810099910004312');
});

it('updates only provided fields (partial update)', function (): void {
    $user = User::factory()->artist()->create([
        'stage_name' => 'Original Name',
        'phone' => '+79001234567',
    ]);

    $payload = [
        'stage_name' => 'New Stage Name',
    ];

    $response = $this->actingAs($user)->patchJson('/api/v1/profile', $payload);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.stage_name', 'New Stage Name');
    $response->assertJsonPath('data.attributes.phone', '+79001234567');
});

it('encrypts passport_data and bank_details in database', function (): void {
    $user = User::factory()->artist()->create();

    $payload = [
        'passport_data' => 'Паспорт: 1234 567890',
        'bank_details' => 'Р/С: 40817810099910004312',
    ];

    $this->actingAs($user)->patchJson('/api/v1/profile', $payload)->assertOk();

    // Verify data is encrypted at rest (raw DB value differs from plain text)
    $rawUser = DB::table('users')
        ->where('id', $user->id)
        ->first();

    expect($rawUser->passport_data)->not->toBe('Паспорт: 1234 567890');
    expect($rawUser->bank_details)->not->toBe('Р/С: 40817810099910004312');

    // But model decrypts it correctly
    $user->refresh();
    expect($user->passport_data)->toBe('Паспорт: 1234 567890');
    expect($user->bank_details)->toBe('Р/С: 40817810099910004312');
});

it('logs PII access when profile is updated', function (): void {
    $user = User::factory()->artist()->create();

    $this->actingAs($user)->patchJson('/api/v1/profile', [
        'passport_data' => 'New passport data',
    ]);

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $user->id,
        'subject_type' => User::class,
        'subject_id' => $user->id,
        'action' => 'profile.updated',
    ]);
});

it('stores IP address in activity log', function (): void {
    $user = User::factory()->artist()->create();

    $this->actingAs($user)->getJson('/api/v1/profile');

    $log = ActivityLog::where('user_id', $user->id)->first();
    expect($log->ip_address)->not->toBeNull();
});

it('returns 401 for unauthenticated profile update', function (): void {
    $response = $this->patchJson('/api/v1/profile', [
        'stage_name' => 'DJ Test',
    ]);

    $response->assertUnauthorized();
});

it('returns 422 when phone format is invalid', function (): void {
    $user = User::factory()->artist()->create();

    $response = $this->actingAs($user)->patchJson('/api/v1/profile', [
        'phone' => 'not-a-phone',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('phone');
});

it('returns 422 when stage_name exceeds max length', function (): void {
    $user = User::factory()->artist()->create();

    $response = $this->actingAs($user)->patchJson('/api/v1/profile', [
        'stage_name' => str_repeat('a', 256),
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('stage_name');
});

it('returns 422 when telegram handle is invalid', function (): void {
    $user = User::factory()->artist()->create();

    $response = $this->actingAs($user)->patchJson('/api/v1/profile', [
        'telegram' => 'no-at-sign',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('telegram');
});

it('allows admin to update any user profile', function (): void {
    $admin = User::factory()->admin()->create();
    $artist = User::factory()->artist()->create();

    $response = $this->actingAs($admin)->patchJson("/api/v1/profile/{$artist->key}", [
        'stage_name' => 'Admin Set Name',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.stage_name', 'Admin Set Name');
});

it('forbids artist from updating other user profile', function (): void {
    $artist1 = User::factory()->artist()->create();
    $artist2 = User::factory()->artist()->create();

    $response = $this->actingAs($artist1)->patchJson("/api/v1/profile/{$artist2->key}", [
        'stage_name' => 'Hacked Name',
    ]);

    $response->assertForbidden();
});

it('allows updating name field', function (): void {
    $user = User::factory()->artist()->create(['name' => 'Old Name']);

    $response = $this->actingAs($user)->patchJson('/api/v1/profile', [
        'name' => 'New Name',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.name', 'New Name');
});

it('strips HTML tags from name and stage_name on update', function (): void {
    $user = User::factory()->artist()->create();

    $response = $this->actingAs($user)->patchJson('/api/v1/profile', [
        'name' => '<script>alert("xss")</script>Test',
        'stage_name' => '<b>Bold</b> Name',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.name', 'alert("xss")Test');
    $response->assertJsonPath('data.attributes.stage_name', 'Bold Name');
});

it('accepts empty body for profile update without changes', function (): void {
    $user = User::factory()->artist()->create(['stage_name' => 'Existing']);

    $response = $this->actingAs($user)->patchJson('/api/v1/profile', []);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.stage_name', 'Existing');
});

it('does not allow changing email or role via profile update', function (): void {
    $user = User::factory()->artist()->create([
        'email' => 'original@example.com',
    ]);

    $response = $this->actingAs($user)->patchJson('/api/v1/profile', [
        'email' => 'hacked@example.com',
        'role' => 'admin',
    ]);

    $response->assertOk();
    $user->refresh();
    expect($user->email)->toBe('original@example.com');
    expect($user->role)->toBe(UserRole::Artist);
});

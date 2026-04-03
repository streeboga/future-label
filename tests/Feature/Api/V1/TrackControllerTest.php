<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\TrackController;
use App\Models\Release;
use App\Models\Track;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

covers(TrackController::class);

// --- POST /api/v1/releases/{release}/tracks (Store) ---

it('creates a track for a draft release and returns 201', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);

    $payload = [
        'title' => 'Track One',
        'format' => 'wav',
        'duration_seconds' => 240,
        'file_url' => 'https://storage.example.com/tracks/track1.wav',
        'file_size' => 50_000_000,
        'authors' => 'John Doe',
        'composers' => 'Jane Doe',
    ];

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/tracks", $payload);

    $response->assertStatus(201);
    $response->assertHeader('Location');
    $response->assertJsonStructure([
        'data' => [
            'type',
            'id',
            'attributes' => [
                'title',
                'track_number',
                'format',
                'duration_seconds',
                'file_url',
            ],
            'links' => ['self'],
        ],
    ]);
    $response->assertJsonPath('data.type', 'tracks');
    $response->assertJsonPath('data.attributes.title', 'Track One');
    $response->assertJsonPath('data.attributes.format', 'wav');
    $response->assertJsonPath('data.attributes.track_number', 1);
});

it('auto-increments track_number when not provided', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);
    Track::factory()->create(['release_id' => $release->id, 'track_number' => 1]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/tracks", [
        'title' => 'Track Two',
        'format' => 'flac',
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.attributes.track_number', 2);
});

it('uses provided track_number', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/tracks", [
        'title' => 'Custom Number Track',
        'format' => 'mp3',
        'track_number' => 5,
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.attributes.track_number', 5);
});

it('returns 422 when title is missing', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/tracks", [
        'format' => 'wav',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('title');
});

it('returns 422 when format is missing', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/tracks", [
        'title' => 'No Format',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('format');
});

it('returns 422 when format is invalid', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/tracks", [
        'title' => 'Bad Format',
        'format' => 'ogg',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrorFor('format');
});

it('rejects track creation on non-draft release', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->inReview()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/tracks", [
        'title' => 'Cannot Add',
        'format' => 'wav',
    ]);

    $response->assertUnprocessable();
});

it('allows track creation on rejected release', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->rejected()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/tracks", [
        'title' => 'Fix Track',
        'format' => 'wav',
    ]);

    $response->assertStatus(201);
});

it('rejects track creation when max 30 tracks reached', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);
    Track::factory()->count(30)->create(['release_id' => $release->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/tracks", [
        'title' => 'Too Many',
        'format' => 'wav',
    ]);

    $response->assertUnprocessable();
});

it('generates key with trk_ prefix', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);

    $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/tracks", [
        'title' => 'Key Check',
        'format' => 'wav',
    ]);

    $track = Track::first();
    expect($track->key)->toStartWith('trk_');
});

it('returns 401 for unauthenticated track creation', function (): void {
    $release = Release::factory()->draft()->create();

    $response = $this->postJson("/api/v1/releases/{$release->key}/tracks", [
        'title' => 'Unauthorized',
        'format' => 'wav',
    ]);

    $response->assertUnauthorized();
});

it('forbids artist from adding track to another user release', function (): void {
    $artist1 = User::factory()->artist()->create();
    $artist2 = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $artist2->id]);

    $response = $this->actingAs($artist1)->postJson("/api/v1/releases/{$release->key}/tracks", [
        'title' => 'Hacked Track',
        'format' => 'wav',
    ]);

    $response->assertForbidden();
});

// --- GET /api/v1/releases/{release}/tracks (Index) ---

it('lists tracks for a release', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);
    Track::factory()->count(3)->create(['release_id' => $release->id]);

    $response = $this->actingAs($user)->getJson("/api/v1/releases/{$release->key}/tracks");

    $response->assertOk();
    $response->assertJsonCount(3, 'data');
});

it('returns tracks ordered by track_number', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);
    Track::factory()->create(['release_id' => $release->id, 'track_number' => 3, 'title' => 'Third']);
    Track::factory()->create(['release_id' => $release->id, 'track_number' => 1, 'title' => 'First']);
    Track::factory()->create(['release_id' => $release->id, 'track_number' => 2, 'title' => 'Second']);

    $response = $this->actingAs($user)->getJson("/api/v1/releases/{$release->key}/tracks");

    $response->assertOk();
    $response->assertJsonPath('data.0.attributes.title', 'First');
    $response->assertJsonPath('data.1.attributes.title', 'Second');
    $response->assertJsonPath('data.2.attributes.title', 'Third');
});

// --- DELETE /api/v1/releases/{release}/tracks/{track} (Destroy) ---

it('deletes a track from a draft release and returns 204', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);
    $track = Track::factory()->create(['release_id' => $release->id]);

    $response = $this->actingAs($user)->deleteJson("/api/v1/releases/{$release->key}/tracks/{$track->key}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('tracks', ['id' => $track->id]);
});

it('rejects track deletion on non-draft release', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->inReview()->create(['user_id' => $user->id]);
    $track = Track::factory()->create(['release_id' => $release->id]);

    $response = $this->actingAs($user)->deleteJson("/api/v1/releases/{$release->key}/tracks/{$track->key}");

    $response->assertUnprocessable();
});

it('forbids artist from deleting track on another user release', function (): void {
    $artist1 = User::factory()->artist()->create();
    $artist2 = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $artist2->id]);
    $track = Track::factory()->create(['release_id' => $release->id]);

    $response = $this->actingAs($artist1)->deleteJson("/api/v1/releases/{$release->key}/tracks/{$track->key}");

    $response->assertForbidden();
});

it('creates track with lyrics and isrc', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/tracks", [
        'title' => 'Lyrical Track',
        'format' => 'flac',
        'lyrics' => 'Some lyrics here',
        'isrc' => 'US-S1Z-23-00001',
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.attributes.lyrics', 'Some lyrics here');
    $response->assertJsonPath('data.attributes.isrc', 'US-S1Z-23-00001');
});

it('strips HTML from track title', function (): void {
    $user = User::factory()->artist()->create();
    $release = Release::factory()->draft()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/v1/releases/{$release->key}/tracks", [
        'title' => '<b>Bold</b> Track',
        'format' => 'wav',
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.attributes.title', 'Bold Track');
});

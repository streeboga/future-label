<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AdminDashboardController;
use App\Models\Payment;
use App\Models\Release;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

covers(AdminDashboardController::class);

it('returns real dashboard metrics for admin', function (): void {
    $admin = User::factory()->admin()->create();
    $artist = User::factory()->artist()->create();

    // Create releases this month owned by the artist
    Release::factory()->count(2)->create(['user_id' => $artist->id]);
    $inReviewRelease = Release::factory()->inReview()->create(['user_id' => $artist->id]);

    // Create confirmed payments with explicit user_id, release_id, and confirmed_by
    Payment::factory()->confirmed()->create([
        'amount' => '500.00',
        'user_id' => $artist->id,
        'release_id' => $inReviewRelease->id,
        'confirmed_by' => $admin->id,
    ]);
    Payment::factory()->confirmed()->create([
        'amount' => '300.00',
        'user_id' => $artist->id,
        'release_id' => $inReviewRelease->id,
        'confirmed_by' => $admin->id,
    ]);
    Payment::factory()->pending()->create([
        'amount' => '100.00',
        'user_id' => $artist->id,
        'release_id' => $inReviewRelease->id,
    ]);

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/dashboard');

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'type',
            'id',
            'attributes' => [
                'total_artists',
                'releases_this_month',
                'total_revenue',
                'pending_moderation',
            ],
        ],
    ]);

    $response->assertJsonPath('data.type', 'dashboard');
    // 1 artist + 1 admin = 2 users total
    $response->assertJsonPath('data.attributes.total_artists', 2);
    // 2 draft + 1 in_review = 3 releases this month
    $response->assertJsonPath('data.attributes.releases_this_month', 3);
    $response->assertJsonPath('data.attributes.total_revenue', '800.00');
    $response->assertJsonPath('data.attributes.pending_moderation', 1);
});

it('returns zeroes when no data exists for admin dashboard', function (): void {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/dashboard');

    $response->assertOk();
    $response->assertJsonPath('data.attributes.total_artists', 1); // the admin itself
    $response->assertJsonPath('data.attributes.releases_this_month', 0);
    $response->assertJsonPath('data.attributes.total_revenue', '0.00');
    $response->assertJsonPath('data.attributes.pending_moderation', 0);
});

it('forbids artist from accessing dashboard', function (): void {
    $artist = User::factory()->artist()->create();

    $response = $this->actingAs($artist)->getJson('/api/v1/admin/dashboard');

    $response->assertForbidden();
});

it('returns 401 for unauthenticated dashboard request', function (): void {
    $response = $this->getJson('/api/v1/admin/dashboard');

    $response->assertUnauthorized();
});

it('allows manager to access dashboard', function (): void {
    $manager = User::factory()->manager()->create();

    $response = $this->actingAs($manager)->getJson('/api/v1/admin/dashboard');

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'type',
            'attributes' => [
                'total_artists',
                'releases_this_month',
                'total_revenue',
                'pending_moderation',
            ],
        ],
    ]);
});

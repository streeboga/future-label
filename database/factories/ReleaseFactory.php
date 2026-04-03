<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ReleaseStatus;
use App\Enums\ReleaseType;
use App\Models\Release;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Release>
 */
final class ReleaseFactory extends Factory
{
    protected $model = Release::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => 'rel_'.Str::ulid(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'artist_name' => fake()->name(),
            'type' => ReleaseType::Single,
            'genre' => fake()->randomElement(['pop', 'rock', 'hip-hop', 'electronic', 'jazz']),
            'language' => 'ru',
            'description' => fake()->paragraph(),
            'release_date' => fake()->dateTimeBetween('+1 week', '+6 months'),
            'cover_url' => null,
            'status' => ReleaseStatus::Draft,
            'reject_reason' => null,
            'metadata' => null,
            'submitted_at' => null,
            'published_at' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ReleaseStatus::Draft,
        ]);
    }

    public function awaitingPayment(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ReleaseStatus::AwaitingPayment,
            'submitted_at' => now(),
        ]);
    }

    public function awaitingContract(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ReleaseStatus::AwaitingContract,
            'submitted_at' => now(),
        ]);
    }

    public function inReview(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ReleaseStatus::InReview,
            'submitted_at' => now(),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ReleaseStatus::Approved,
            'submitted_at' => now(),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ReleaseStatus::Published,
            'submitted_at' => now(),
            'published_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ReleaseStatus::Rejected,
            'reject_reason' => 'Quality standards not met.',
            'submitted_at' => now(),
        ]);
    }

    public function single(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => ReleaseType::Single,
        ]);
    }

    public function ep(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => ReleaseType::Ep,
        ]);
    }

    public function album(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => ReleaseType::Album,
        ]);
    }
}

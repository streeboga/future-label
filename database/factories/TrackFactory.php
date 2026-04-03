<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TrackFormat;
use App\Models\Release;
use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Track>
 */
final class TrackFactory extends Factory
{
    protected $model = Track::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => 'trk_'.Str::ulid(),
            'release_id' => Release::factory(),
            'title' => fake()->sentence(3),
            'track_number' => 1,
            'duration_seconds' => fake()->numberBetween(60, 600),
            'file_url' => 'https://storage.example.com/tracks/'.Str::uuid().'.wav',
            'format' => TrackFormat::Wav,
            'file_size' => fake()->numberBetween(1_000_000, 100_000_000),
            'authors' => fake()->name(),
            'composers' => fake()->name(),
            'lyrics' => null,
            'isrc' => 'US-S1Z-23-00001',
        ];
    }

    public function wav(): static
    {
        return $this->state(fn (array $attributes): array => [
            'format' => TrackFormat::Wav,
        ]);
    }

    public function flac(): static
    {
        return $this->state(fn (array $attributes): array => [
            'format' => TrackFormat::Flac,
        ]);
    }

    public function mp3(): static
    {
        return $this->state(fn (array $attributes): array => [
            'format' => TrackFormat::Mp3,
        ]);
    }
}

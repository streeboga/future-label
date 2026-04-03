<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\NotificationType;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Notification>
 */
final class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => 'ntf_'.Str::ulid(),
            'user_id' => User::factory(),
            'type' => NotificationType::ReleaseStatusChanged,
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'read_at' => null,
            'data' => null,
        ];
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes): array => [
            'read_at' => now(),
        ]);
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes): array => [
            'read_at' => null,
        ]);
    }

    public function releaseStatusChanged(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => NotificationType::ReleaseStatusChanged,
            'title' => 'Release status changed',
        ]);
    }

    public function paymentConfirmed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => NotificationType::PaymentConfirmed,
            'title' => 'Payment confirmed',
        ]);
    }

    public function contractGenerated(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => NotificationType::ContractGenerated,
            'title' => 'Contract generated',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\ServiceCatalog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
final class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => 'ord_'.Str::ulid(),
            'user_id' => User::factory(),
            'release_id' => null,
            'service_id' => ServiceCatalog::factory(),
            'status' => OrderStatus::Pending,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function withStatus(OrderStatus $status): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => $status,
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }

    public function forService(ServiceCatalog $service): static
    {
        return $this->state(fn (array $attributes): array => [
            'service_id' => $service->id,
        ]);
    }
}

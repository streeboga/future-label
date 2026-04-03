<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Release;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
final class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => 'pay_'.Str::ulid(),
            'user_id' => User::factory(),
            'release_id' => Release::factory(),
            'order_id' => null,
            'amount' => fake()->randomFloat(2, 100, 10000),
            'currency' => 'RUB',
            'method' => PaymentMethod::Online,
            'status' => PaymentStatus::Pending,
            'provider' => 'stub',
            'provider_payment_id' => null,
            'receipt_url' => null,
            'confirmed_by' => null,
            'confirmed_at' => null,
            'provider_data' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => PaymentStatus::Pending,
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => PaymentStatus::Processing,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => PaymentStatus::Paid,
            'provider_payment_id' => 'stub_'.Str::random(20),
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => PaymentStatus::Confirmed,
            'confirmed_at' => now(),
            'confirmed_by' => User::factory()->admin(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => PaymentStatus::Failed,
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => PaymentStatus::Refunded,
        ]);
    }

    public function online(): static
    {
        return $this->state(fn (array $attributes): array => [
            'method' => PaymentMethod::Online,
        ]);
    }

    public function manual(): static
    {
        return $this->state(fn (array $attributes): array => [
            'method' => PaymentMethod::Manual,
        ]);
    }
}

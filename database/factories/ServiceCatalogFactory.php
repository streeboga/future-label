<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ServiceCategory;
use App\Models\ServiceCatalog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ServiceCatalog>
 */
final class ServiceCatalogFactory extends Factory
{
    protected $model = ServiceCatalog::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => 'svc_'.Str::ulid(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 100, 50000),
            'currency' => 'RUB',
            'category' => fake()->randomElement(ServiceCategory::cases()),
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }

    public function withCategory(ServiceCategory $category): static
    {
        return $this->state(fn (array $attributes): array => [
            'category' => $category,
        ]);
    }

    public function withSortOrder(int $order): static
    {
        return $this->state(fn (array $attributes): array => [
            'sort_order' => $order,
        ]);
    }
}

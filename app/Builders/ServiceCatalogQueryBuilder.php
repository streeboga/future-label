<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enums\ServiceCategory;
use App\Models\ServiceCatalog;
use Illuminate\Database\Eloquent\Builder;

final class ServiceCatalogQueryBuilder
{
    /**
     * @param  Builder<ServiceCatalog>  $query
     */
    public function __construct(
        private Builder $query,
    ) {}

    public static function make(): self
    {
        return new self(ServiceCatalog::query());
    }

    /**
     * @return Builder<ServiceCatalog>
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }

    public function byKey(string $key): self
    {
        $this->query->where('key', $key);

        return $this;
    }

    public function active(): self
    {
        $this->query->where('is_active', true);

        return $this;
    }

    public function withCategory(ServiceCategory $category): self
    {
        $this->query->where('category', $category->value);

        return $this;
    }

    public function search(string $term): self
    {
        $escaped = str_replace(['%', '_'], ['\%', '\_'], $term);

        $this->query->where(function (Builder $q) use ($escaped): void {
            $q->where('title', 'like', "%{$escaped}%")
                ->orWhere('description', 'like', "%{$escaped}%");
        });

        return $this;
    }

    public function sortBySortOrder(string $direction = 'asc'): self
    {
        $this->query->orderBy('sort_order', $direction);

        return $this;
    }

    public function sortBy(string $column, string $direction = 'asc'): self
    {
        $allowed = ['created_at', 'updated_at', 'title', 'price', 'sort_order'];
        if (in_array($column, $allowed, true)) {
            $this->query->orderBy($column, $direction);
        }

        return $this;
    }
}

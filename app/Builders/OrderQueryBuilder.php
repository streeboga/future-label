<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

final class OrderQueryBuilder
{
    /**
     * @param  Builder<Order>  $query
     */
    public function __construct(
        private Builder $query,
    ) {}

    public static function make(): self
    {
        return new self(Order::query());
    }

    /**
     * @return Builder<Order>
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

    public function forUser(int $userId): self
    {
        $this->query->where('user_id', $userId);

        return $this;
    }

    public function withStatus(OrderStatus $status): self
    {
        $this->query->where('status', $status->value);

        return $this;
    }

    public function withRelations(): self
    {
        $this->query->with(['service', 'release', 'user']);

        return $this;
    }

    public function sortBy(string $column, string $direction = 'desc'): self
    {
        $allowed = ['created_at', 'updated_at', 'status'];
        if (in_array($column, $allowed, true)) {
            $this->query->orderBy($column, $direction);
        }

        return $this;
    }
}

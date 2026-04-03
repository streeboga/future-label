<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Builders\OrderQueryBuilder;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class OrderRepository implements OrderRepositoryInterface
{
    /**
     * @return Collection<int, Order>
     */
    public function allForUser(int $userId): Collection
    {
        return OrderQueryBuilder::make()
            ->forUser($userId)
            ->withRelations()
            ->sortBy('created_at', 'desc')
            ->getQuery()
            ->get();
    }

    public function findByKey(string $key): Order
    {
        $model = OrderQueryBuilder::make()
            ->byKey($key)
            ->withRelations()
            ->getQuery()
            ->first();

        if (! $model) {
            throw new ModelNotFoundException("Order not found with key [{$key}].");
        }

        return $model;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Order
    {
        return Order::create($data);
    }
}

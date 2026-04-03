<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    /**
     * @return Collection<int, Order>
     */
    public function allForUser(int $userId): Collection;

    public function findByKey(string $key): Order;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Order;
}

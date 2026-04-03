<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\Order\CreateOrderData;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final readonly class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $repository,
    ) {}

    /**
     * @return Collection<int, Order>
     */
    public function listForUser(User $user): Collection
    {
        return $this->repository->allForUser($user->id);
    }

    public function create(User $user, CreateOrderData $data): Order
    {
        /** @var Order */
        return DB::transaction(fn (): Order => $this->repository->create([
            'user_id' => $user->id,
            'service_id' => $data->service_id,
            'release_id' => $data->release_id,
            'status' => OrderStatus::Pending,
            'notes' => $data->notes,
        ]));
    }

    public function findByKey(string $key): Order
    {
        return $this->repository->findByKey($key);
    }
}

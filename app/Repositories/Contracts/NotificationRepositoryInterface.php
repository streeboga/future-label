<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Notification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface NotificationRepositoryInterface
{
    public function findByKey(string $key): Notification;

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Notification>
     */
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Notification;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Notification $notification, array $data): Notification;

    public function markAsRead(Notification $notification): Notification;
}

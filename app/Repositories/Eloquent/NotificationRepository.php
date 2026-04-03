<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Builders\NotificationQueryBuilder;
use App\Enums\NotificationType;
use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class NotificationRepository implements NotificationRepositoryInterface
{
    public function findByKey(string $key): Notification
    {
        $model = NotificationQueryBuilder::make()
            ->byKey($key)
            ->getQuery()
            ->first();

        if (! $model) {
            throw new ModelNotFoundException("Notification not found with key [{$key}].");
        }

        return $model;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Notification>
     */
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $builder = NotificationQueryBuilder::make()
            ->byUserId($userId)
            ->latest();

        $this->applyFilters($builder, $filters);

        return $builder->getQuery()->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Notification $notification, array $data): Notification
    {
        $notification->fill($data);
        $notification->save();

        return $notification->refresh();
    }

    public function markAsRead(Notification $notification): Notification
    {
        $notification->read_at = now();
        $notification->save();

        return $notification->refresh();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(NotificationQueryBuilder $builder, array $filters): void
    {
        if (isset($filters['type']) && $filters['type'] instanceof NotificationType) {
            $builder->withType($filters['type']);
        }

        if (isset($filters['unread']) && $filters['unread'] === true) {
            $builder->unreadOnly();
        }
    }
}

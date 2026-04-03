<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\Notification\CreateNotificationData;
use App\Enums\NotificationType;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final readonly class NotificationService
{
    public function __construct(
        private NotificationRepositoryInterface $repository,
    ) {}

    public function create(CreateNotificationData $data): Notification
    {
        /** @var Notification */
        return DB::transaction(fn (): Notification => $this->repository->create([
            'user_id' => $data->user_id,
            'type' => $data->type,
            'title' => $data->title,
            'body' => $data->body,
            'data' => $data->data,
        ]));
    }

    public function markAsRead(Notification $notification): Notification
    {
        /** @var Notification */
        return DB::transaction(fn (): Notification => $this->repository->markAsRead($notification));
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Notification>
     */
    public function listForUser(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateForUser($user->id, $filters, $perPage);
    }

    public function findByKey(string $key): Notification
    {
        return $this->repository->findByKey($key);
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    public function createForUser(
        int $userId,
        NotificationType $type,
        string $title,
        string $body,
        ?array $data = null,
    ): Notification {
        return $this->create(new CreateNotificationData(
            user_id: $userId,
            type: $type,
            title: $title,
            body: $body,
            data: $data,
        ));
    }
}

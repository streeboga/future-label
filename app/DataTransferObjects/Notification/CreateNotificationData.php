<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Notification;

use App\Enums\NotificationType;
use Spatie\LaravelData\Data;

final class CreateNotificationData extends Data
{
    /**
     * @param  array<string, mixed>|null  $data
     */
    public function __construct(
        public readonly int $user_id,
        public readonly NotificationType $type,
        public readonly string $title,
        public readonly string $body,
        public readonly ?array $data = null,
    ) {}
}

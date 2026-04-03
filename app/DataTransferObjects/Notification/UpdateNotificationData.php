<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Notification;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class UpdateNotificationData extends Data
{
    public function __construct(
        public readonly string|Optional|null $read_at,
    ) {}
}

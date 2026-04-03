<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\TelegramNotificationService;

final readonly class SendNewArtistTelegramNotification
{
    public function __construct(
        private TelegramNotificationService $telegramService,
    ) {}

    public function handle(UserRegistered $event): void
    {
        $user = $event->user;

        $this->telegramService->send(
            "<b>New Artist Registered</b>\nName: {$user->name}\nEmail: {$user->email}",
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ReleaseSubmittedForReview;
use App\Services\TelegramNotificationService;

final readonly class SendReleaseSubmittedTelegramNotification
{
    public function __construct(
        private TelegramNotificationService $telegramService,
    ) {}

    public function handle(ReleaseSubmittedForReview $event): void
    {
        $release = $event->release;
        $release->loadMissing('user');

        $this->telegramService->send(
            "<b>New Release Submitted</b>\nTitle: {$release->title}\nArtist: {$release->user->name}\nType: {$release->type->value}",
        );
    }
}

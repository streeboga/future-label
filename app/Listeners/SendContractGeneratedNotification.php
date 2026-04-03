<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\NotificationType;
use App\Events\ContractGenerated;
use App\Notifications\ContractGeneratedNotification;
use App\Services\NotificationService;

final readonly class SendContractGeneratedNotification
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function handle(ContractGenerated $event): void
    {
        // Store in-app notification
        $this->notificationService->createForUser(
            userId: $event->user->id,
            type: NotificationType::ContractGenerated,
            title: "Contract ready — {$event->release->title}",
            body: "The contract for your release \"{$event->release->title}\" is ready for download.",
            data: [
                'release_key' => $event->release->key,
                'contract_url' => $event->contractUrl,
            ],
        );

        // Send email
        $event->user->notify(new ContractGeneratedNotification(
            releaseTitle: $event->release->title,
            contractUrl: $event->contractUrl,
        ));
    }
}

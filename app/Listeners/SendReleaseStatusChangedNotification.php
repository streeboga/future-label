<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\NotificationType;
use App\Enums\ReleaseStatus;
use App\Events\ReleaseStatusChanged;
use App\Notifications\ReleaseStatusChangedNotification;
use App\Services\NotificationService;

final readonly class SendReleaseStatusChangedNotification
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function handle(ReleaseStatusChanged $event): void
    {
        $release = $event->release;
        $release->loadMissing('user');

        $notifiableStatuses = [
            ReleaseStatus::Approved,
            ReleaseStatus::Rejected,
            ReleaseStatus::Published,
        ];

        if (! in_array($event->newStatus, $notifiableStatuses, true)) {
            return;
        }

        // Store in-app notification
        $this->notificationService->createForUser(
            userId: $release->user_id,
            type: NotificationType::ReleaseStatusChanged,
            title: "Release \"{$release->title}\" — {$event->newStatus->getLabel()}",
            body: "Status changed from {$event->oldStatus->getLabel()} to {$event->newStatus->getLabel()}.",
            data: [
                'release_key' => $release->key,
                'old_status' => $event->oldStatus->value,
                'new_status' => $event->newStatus->value,
            ],
        );

        // Send email
        $release->user->notify(new ReleaseStatusChangedNotification(
            releaseTitle: $release->title,
            oldStatus: $event->oldStatus,
            newStatus: $event->newStatus,
            rejectReason: $release->reject_reason,
        ));
    }
}

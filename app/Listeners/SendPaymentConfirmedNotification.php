<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\NotificationType;
use App\Events\PaymentConfirmed;
use App\Notifications\PaymentConfirmedNotification;
use App\Services\NotificationService;
use App\Services\TelegramNotificationService;

final readonly class SendPaymentConfirmedNotification
{
    public function __construct(
        private NotificationService $notificationService,
        private TelegramNotificationService $telegramService,
    ) {}

    public function handle(PaymentConfirmed $event): void
    {
        $order = $event->order;
        $order->loadMissing(['user', 'service']);

        // Store in-app notification
        $this->notificationService->createForUser(
            userId: $order->user_id,
            type: NotificationType::PaymentConfirmed,
            title: "Payment confirmed — Order {$order->key}",
            body: "Your payment for \"{$order->service->title}\" has been confirmed.",
            data: [
                'order_key' => $order->key,
            ],
        );

        // Send email to artist
        $order->user->notify(new PaymentConfirmedNotification(
            orderKey: $order->key,
            serviceName: $order->service->title,
        ));

        // Telegram notification to team
        $this->telegramService->send(
            "<b>Payment Received</b>\nOrder: {$order->key}\nArtist: {$order->user->name}\nService: {$order->service->title}",
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class PaymentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $orderKey,
        private readonly string $serviceName,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Оплата подтверждена — Заказ {$this->orderKey}")
            ->greeting('Оплата получена')
            ->line("Ваш платёж за услугу **{$this->serviceName}** (заказ {$this->orderKey}) подтверждён.")
            ->salutation('Спасибо, ' . config('app.name'));
    }
}

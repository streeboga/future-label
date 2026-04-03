<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class ContractGeneratedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $releaseTitle,
        private readonly string $contractUrl,
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
            ->subject("Договор готов — {$this->releaseTitle}")
            ->greeting('Договор сформирован')
            ->line("Договор для релиза **{$this->releaseTitle}** готов к подписанию.")
            ->line('Перейдите в личный кабинет для просмотра и подписания.')
            ->salutation('Спасибо, ' . config('app.name'));
    }
}

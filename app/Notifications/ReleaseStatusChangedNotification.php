<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\ReleaseStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class ReleaseStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $releaseTitle,
        private readonly ReleaseStatus $oldStatus,
        private readonly ReleaseStatus $newStatus,
        private readonly ?string $rejectReason = null,
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
        $mail = (new MailMessage)
            ->subject("Релиз \"{$this->releaseTitle}\" — {$this->newStatus->getLabel()}")
            ->greeting("Статус релиза обновлён")
            ->line("Ваш релиз **{$this->releaseTitle}** получил новый статус.")
            ->line("**Прежний статус:** {$this->oldStatus->getLabel()}")
            ->line("**Новый статус:** {$this->newStatus->getLabel()}");

        if ($this->rejectReason !== null) {
            $mail->line("**Причина:** {$this->rejectReason}");
        }

        return $mail->salutation('Спасибо, ' . config('app.name'));
    }
}

<?php

declare(strict_types=1);

namespace App\Enums;

use App\Contracts\Enums\HasColor;
use App\Contracts\Enums\HasIcon;
use App\Contracts\Enums\HasLabel;

enum NotificationType: string implements HasColor, HasIcon, HasLabel
{
    case ReleaseStatusChanged = 'release_status_changed';
    case PaymentConfirmed = 'payment_confirmed';
    case ContractGenerated = 'contract_generated';
    case ReleaseSubmitted = 'release_submitted';
    case NewArtistRegistered = 'new_artist_registered';

    public function getLabel(): string
    {
        return match ($this) {
            self::ReleaseStatusChanged => 'Изменение статуса релиза',
            self::PaymentConfirmed => 'Оплата подтверждена',
            self::ContractGenerated => 'Договор готов',
            self::ReleaseSubmitted => 'Релиз отправлен на проверку',
            self::NewArtistRegistered => 'Новый артист зарегистрирован',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ReleaseStatusChanged => 'info',
            self::PaymentConfirmed => 'success',
            self::ContractGenerated => 'primary',
            self::ReleaseSubmitted => 'warning',
            self::NewArtistRegistered => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ReleaseStatusChanged => 'heroicon-o-arrow-path',
            self::PaymentConfirmed => 'heroicon-o-credit-card',
            self::ContractGenerated => 'heroicon-o-document-text',
            self::ReleaseSubmitted => 'heroicon-o-paper-airplane',
            self::NewArtistRegistered => 'heroicon-o-user-plus',
        };
    }

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

<?php

declare(strict_types=1);

namespace App\Enums;

use App\Contracts\Enums\HasColor;
use App\Contracts\Enums\HasIcon;
use App\Contracts\Enums\HasLabel;

enum PaymentMethod: string implements HasColor, HasIcon, HasLabel
{
    case Online = 'online';
    case Manual = 'manual';

    public function getLabel(): string
    {
        return match ($this) {
            self::Online => 'Онлайн оплата',
            self::Manual => 'Ручная оплата',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Online => 'info',
            self::Manual => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Online => 'heroicon-o-credit-card',
            self::Manual => 'heroicon-o-banknotes',
        };
    }

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn (self $case): string => $case->getLabel(), self::cases())
        );
    }
}

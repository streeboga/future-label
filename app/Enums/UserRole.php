<?php

declare(strict_types=1);

namespace App\Enums;

use App\Contracts\Enums\HasColor;
use App\Contracts\Enums\HasIcon;
use App\Contracts\Enums\HasLabel;

enum UserRole: string implements HasColor, HasIcon, HasLabel
{
    case Artist = 'artist';
    case Manager = 'manager';
    case Admin = 'admin';

    public function getLabel(): string
    {
        return match ($this) {
            self::Artist => 'Артист',
            self::Manager => 'Менеджер',
            self::Admin => 'Администратор',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Artist => 'info',
            self::Manager => 'warning',
            self::Admin => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Artist => 'heroicon-o-musical-note',
            self::Manager => 'heroicon-o-briefcase',
            self::Admin => 'heroicon-o-shield-check',
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

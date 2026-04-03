<?php

declare(strict_types=1);

namespace App\Enums;

use App\Contracts\Enums\HasColor;
use App\Contracts\Enums\HasIcon;
use App\Contracts\Enums\HasLabel;

enum ReleaseType: string implements HasColor, HasIcon, HasLabel
{
    case Single = 'single';
    case Ep = 'ep';
    case Album = 'album';

    public function getLabel(): string
    {
        return match ($this) {
            self::Single => 'Сингл',
            self::Ep => 'EP',
            self::Album => 'Альбом',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Single => 'info',
            self::Ep => 'warning',
            self::Album => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Single => 'heroicon-o-musical-note',
            self::Ep => 'heroicon-o-rectangle-stack',
            self::Album => 'heroicon-o-circle-stack',
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

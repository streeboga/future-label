<?php

declare(strict_types=1);

namespace App\Enums;

use App\Contracts\Enums\HasColor;
use App\Contracts\Enums\HasIcon;
use App\Contracts\Enums\HasLabel;

enum ServiceCategory: string implements HasColor, HasIcon, HasLabel
{
    case Production = 'production';
    case Mixing = 'mixing';
    case Mastering = 'mastering';
    case Distribution = 'distribution';
    case Promotion = 'promotion';
    case Design = 'design';
    case Video = 'video';
    case Legal = 'legal';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Production => 'Продакшн',
            self::Mixing => 'Сведение',
            self::Mastering => 'Мастеринг',
            self::Distribution => 'Дистрибуция',
            self::Promotion => 'Продвижение',
            self::Design => 'Дизайн',
            self::Video => 'Видео',
            self::Legal => 'Юридические услуги',
            self::Other => 'Прочее',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Production => 'primary',
            self::Mixing => 'info',
            self::Mastering => 'success',
            self::Distribution => 'warning',
            self::Promotion => 'danger',
            self::Design => 'secondary',
            self::Video => 'primary',
            self::Legal => 'warning',
            self::Other => 'secondary',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Production => 'heroicon-o-musical-note',
            self::Mixing => 'heroicon-o-adjustments-horizontal',
            self::Mastering => 'heroicon-o-speaker-wave',
            self::Distribution => 'heroicon-o-globe-alt',
            self::Promotion => 'heroicon-o-megaphone',
            self::Design => 'heroicon-o-paint-brush',
            self::Video => 'heroicon-o-video-camera',
            self::Legal => 'heroicon-o-scale',
            self::Other => 'heroicon-o-ellipsis-horizontal',
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

<?php

declare(strict_types=1);

namespace App\Enums;

use App\Contracts\Enums\HasColor;
use App\Contracts\Enums\HasIcon;
use App\Contracts\Enums\HasLabel;

enum TrackFormat: string implements HasColor, HasIcon, HasLabel
{
    case Wav = 'wav';
    case Flac = 'flac';
    case Mp3 = 'mp3';

    public function getLabel(): string
    {
        return match ($this) {
            self::Wav => 'WAV',
            self::Flac => 'FLAC',
            self::Mp3 => 'MP3',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Wav => 'success',
            self::Flac => 'info',
            self::Mp3 => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Wav => 'heroicon-o-musical-note',
            self::Flac => 'heroicon-o-musical-note',
            self::Mp3 => 'heroicon-o-musical-note',
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

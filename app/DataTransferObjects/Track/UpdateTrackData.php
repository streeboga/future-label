<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Track;

use App\Enums\TrackFormat;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class UpdateTrackData extends Data
{
    public function __construct(
        public readonly string|Optional $title,
        public readonly TrackFormat|Optional $format,
        public readonly int|Optional|null $track_number,
        public readonly int|Optional|null $duration_seconds,
        public readonly string|Optional|null $file_url,
        public readonly int|Optional|null $file_size,
        public readonly string|Optional|null $authors,
        public readonly string|Optional|null $composers,
        public readonly string|Optional|null $lyrics,
        public readonly string|Optional|null $isrc,
    ) {}
}

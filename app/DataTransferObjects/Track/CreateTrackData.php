<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Track;

use App\Enums\TrackFormat;
use Spatie\LaravelData\Data;

final class CreateTrackData extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly TrackFormat $format,
        public readonly ?int $track_number = null,
        public readonly ?int $duration_seconds = null,
        public readonly ?string $file_url = null,
        public readonly ?int $file_size = null,
        public readonly ?string $authors = null,
        public readonly ?string $composers = null,
        public readonly ?string $lyrics = null,
        public readonly ?string $isrc = null,
    ) {}
}

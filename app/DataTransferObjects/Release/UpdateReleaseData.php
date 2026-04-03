<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Release;

use App\Enums\ReleaseType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class UpdateReleaseData extends Data
{
    public function __construct(
        public readonly string|Optional $title,
        public readonly ReleaseType|Optional $type,
        public readonly string|Optional|null $artist_name,
        public readonly string|Optional|null $genre,
        public readonly string|Optional|null $language,
        public readonly string|Optional|null $description,
        public readonly string|Optional|null $release_date,
        public readonly string|Optional|null $cover_url,
        /** @var array<string, mixed>|Optional|null */
        public readonly array|Optional|null $metadata,
    ) {}
}

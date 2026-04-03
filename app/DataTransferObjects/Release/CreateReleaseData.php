<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Release;

use App\Enums\ReleaseType;
use Spatie\LaravelData\Data;

final class CreateReleaseData extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly ReleaseType $type,
        public readonly ?string $artist_name = null,
        public readonly ?string $genre = null,
        public readonly ?string $language = null,
        public readonly ?string $description = null,
        public readonly ?string $release_date = null,
        public readonly ?string $cover_url = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $metadata = null,
    ) {}
}

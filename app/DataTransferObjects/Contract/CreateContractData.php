<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Contract;

use Spatie\LaravelData\Data;

final class CreateContractData extends Data
{
    public function __construct(
        public readonly int $release_id,
        public readonly string $template_version = '1.0',
    ) {}
}

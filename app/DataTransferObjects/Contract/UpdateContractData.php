<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Contract;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class UpdateContractData extends Data
{
    public function __construct(
        public readonly string|Optional $template_version,
    ) {}
}

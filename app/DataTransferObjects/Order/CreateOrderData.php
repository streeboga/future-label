<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Order;

use Spatie\LaravelData\Data;

final class CreateOrderData extends Data
{
    public function __construct(
        public readonly int $service_id,
        public readonly ?int $release_id = null,
        public readonly ?string $notes = null,
    ) {}
}

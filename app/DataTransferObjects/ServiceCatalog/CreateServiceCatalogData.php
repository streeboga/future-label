<?php

declare(strict_types=1);

namespace App\DataTransferObjects\ServiceCatalog;

use App\Enums\ServiceCategory;
use Spatie\LaravelData\Data;

final class CreateServiceCatalogData extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly string $price,
        public readonly ServiceCategory $category,
        public readonly ?string $description = null,
        public readonly string $currency = 'RUB',
        public readonly int $sort_order = 0,
        public readonly bool $is_active = true,
    ) {}
}

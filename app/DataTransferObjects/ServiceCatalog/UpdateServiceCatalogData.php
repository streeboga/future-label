<?php

declare(strict_types=1);

namespace App\DataTransferObjects\ServiceCatalog;

use App\Enums\ServiceCategory;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class UpdateServiceCatalogData extends Data
{
    public function __construct(
        public readonly string|Optional $title,
        public readonly string|Optional|null $description,
        public readonly string|Optional $price,
        public readonly string|Optional $currency,
        public readonly ServiceCategory|Optional $category,
        public readonly int|Optional $sort_order,
        public readonly bool|Optional $is_active,
    ) {}
}

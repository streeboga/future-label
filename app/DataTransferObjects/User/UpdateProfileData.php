<?php

declare(strict_types=1);

namespace App\DataTransferObjects\User;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class UpdateProfileData extends Data
{
    public function __construct(
        public readonly string|Optional $name,
        public readonly string|Optional|null $stage_name,
        public readonly string|Optional|null $phone,
        public readonly string|Optional|null $telegram,
        public readonly string|Optional|null $passport_data,
        public readonly string|Optional|null $bank_details,
    ) {}
}

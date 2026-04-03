<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Payment;

use App\Enums\PaymentStatus;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class UpdatePaymentData extends Data
{
    public function __construct(
        public readonly PaymentStatus|Optional $status = new Optional,
        public readonly string|Optional|null $provider_payment_id = new Optional,
        public readonly string|Optional|null $receipt_url = new Optional,
        /** @var array<string, mixed>|null */
        public readonly array|Optional|null $provider_data = new Optional,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toUpdateArray(): array
    {
        return collect($this->toArray())
            ->reject(fn (mixed $value): bool => $value instanceof Optional)
            ->toArray();
    }
}

<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Payment;

use App\Enums\PaymentMethod;
use Spatie\LaravelData\Data;

final class CreatePaymentData extends Data
{
    public function __construct(
        public readonly PaymentMethod $method,
        public readonly ?string $return_url = null,
    ) {}
}

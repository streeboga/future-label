<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Payment;

final readonly class PaymentResult
{
    /**
     * @param  array<string, mixed>  $providerData
     */
    public function __construct(
        public bool $success,
        public ?string $paymentId = null,
        public ?string $paymentUrl = null,
        public ?string $receiptUrl = null,
        public ?string $errorMessage = null,
        public array $providerData = [],
    ) {}
}

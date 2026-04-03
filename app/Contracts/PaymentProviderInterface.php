<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DataTransferObjects\Payment\PaymentResult;
use Illuminate\Http\Request;

interface PaymentProviderInterface
{
    public function createPayment(string $amount, string $currency, string $description, string $returnUrl): PaymentResult;

    public function verifyWebhook(Request $request): bool;
}

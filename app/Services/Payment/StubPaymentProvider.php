<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Contracts\PaymentProviderInterface;
use App\DataTransferObjects\Payment\PaymentResult;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class StubPaymentProvider implements PaymentProviderInterface
{
    public function createPayment(string $amount, string $currency, string $description, string $returnUrl): PaymentResult
    {
        $paymentId = 'stub_'.Str::random(20);

        return new PaymentResult(
            success: true,
            paymentId: $paymentId,
            paymentUrl: $returnUrl.'?payment_id='.$paymentId,
            receiptUrl: null,
            errorMessage: null,
            providerData: [
                'stub' => true,
                'amount' => $amount,
                'currency' => $currency,
                'description' => $description,
            ],
        );
    }

    public function verifyWebhook(Request $request): bool
    {
        return $request->header('X-Webhook-Secret') === config('payment.webhook_secret', 'stub-secret');
    }
}

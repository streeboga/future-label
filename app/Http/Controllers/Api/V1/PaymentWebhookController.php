<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\PaymentProviderInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class PaymentWebhookController extends Controller
{
    public function __construct(
        private readonly PaymentService $service,
        private readonly PaymentProviderInterface $provider,
    ) {}

    /**
     * Handle payment webhook
     *
     * Processes incoming payment provider webhooks. No authentication required.
     */
    public function __invoke(Request $request): JsonResponse|PaymentResource
    {
        if (! $this->provider->verifyWebhook($request)) {
            return response()->json([
                'errors' => [[
                    'status' => '403',
                    'title' => 'Forbidden',
                    'detail' => 'Invalid webhook signature.',
                ]],
            ], Response::HTTP_FORBIDDEN);
        }

        /** @var array<string, mixed> $webhookData */
        $webhookData = $request->all();

        $payment = $this->service->processWebhook($webhookData);

        return PaymentResource::make($payment);
    }
}

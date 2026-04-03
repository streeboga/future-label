<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

final class AdminPaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $service,
    ) {}

    /**
     * Confirm a manual payment
     *
     * Manager/Admin confirms a manual payment. Transitions payment to confirmed status.
     */
    public function confirm(Request $request, Payment $payment): PaymentResource
    {
        Gate::authorize('confirm', $payment);

        /** @var User $user */
        $user = $request->user();

        $payment = $this->service->confirmPayment($payment, $user);

        return PaymentResource::make($payment);
    }

    /**
     * Reject a payment
     *
     * Manager/Admin rejects a payment. Transitions payment to failed status.
     */
    public function reject(Payment $payment): PaymentResource
    {
        Gate::authorize('reject', $payment);

        $payment = $this->service->rejectPayment($payment);

        return PaymentResource::make($payment);
    }
}
